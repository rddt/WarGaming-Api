<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use WarGaming\Api\Cache\ArrayCache;
use WarGaming\Api\Cache\CacheInterface;
use WarGaming\Api\Exception\ExceptionFactory;
use WarGaming\Api\FormData\FormDataGenerator;
use WarGaming\Api\FormData\FormDataGeneratorInterface;
use WarGaming\Api\Method\MethodInterface;
use WarGaming\Api\Method\ProcessorInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base core for sending request to Payment Gateway API
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Client
{
    const DEFAULT_HOST = 'api.worldoftanks.ru';

    /**
     * @var GuzzleClient
     */
    protected $httpClient;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var FormDataGeneratorInterface
     */
    private $formDataGenerator;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $defaultLanguage = 'en';

    /**
     * @var string
     */
    private $applicationId;

    /**
     * @var string
     */
    private $apiHost;

    /**
     * Use SSL
     *
     * @var bool
     */
    private $apiSecure = true;

    /**
     * Construct
     *
     * @param GuzzleClient               $httpClient
     * @param ValidatorInterface         $validator
     * @param EventDispatcherInterface   $eventDispatcher
     * @param FormDataGeneratorInterface $formDataGenerator
     * @param string                     $apiHost
     * @param bool                       $apiSecure
     */
    public function __construct(
        GuzzleClient $httpClient,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher,
        FormDataGeneratorInterface $formDataGenerator,
        $apiHost = null,
        $apiSecure = true
    )
    {
        $this->httpClient = $httpClient;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
        $this->formDataGenerator = $formDataGenerator;
        $this->apiHost = $apiHost ?: self::DEFAULT_HOST;
        $this->apiSecure = (bool) $apiSecure;
    }

    /**
     * Set application ID
     *
     * @param string $applicationId
     *
     * @return Client
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * Get application ID
     *
     * @return string
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * Get event dispatcher
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Set cache storage
     *
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Set default language
     *
     * @param string $language
     *
     * @return Client
     */
    public function setDefaultLanguage($language)
    {
        $this->defaultLanguage = $language;

        return $this;
    }

    /**
     * Get default language
     *
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * Get request host (Production or testing)
     *
     * @return string
     */
    public function getRequestHost()
    {
        return self::DEFAULT_HOST;
    }

    /**
     * API Request
     *
     * @param MethodInterface $method
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function request(MethodInterface $method)
    {
        $validator = $this->validator;

        $violationList = $validator->validate($method, $method->getValidationGroups());

        if (count($violationList)) {
            throw ExceptionFactory::methodNotValid($violationList);
        }

        $processor = $this->createProcessor($method);

        // Create a http request instance
        $httpRequest = $processor->createRequest($this->httpClient, $method);

        // Set host and scheme to uri
        $httpRequest->setHost($this->apiHost);
        $httpRequest->setScheme($this->apiSecure ? 'https' : 'http');

        // Set language
        if (!$language = $method->getLanguage()) {
            $language = $this->defaultLanguage;
        }

        if ($language) {
            $httpRequest->getQuery()->add('language', $language);
        }

        // Call request start event.
        // Must be call before set authorization info
        $this->dispatch(Events::REQUEST_START, new Events\RequestStartEvent($httpRequest));

        // Set application ID to request
        if (!$this->applicationId) {
            throw ExceptionFactory::missingApplicationId();
        }

        $httpRequest->getQuery()->add('application_id', $this->applicationId);

        // Check request in cache storage
        $requestResponse = null;
        $cacheKey = null;
        $mustCache = $method->isCacheAvailable() && null !== $ttl = $method->getCacheTtl() && $this->cache;

        if ($mustCache) {
            $cacheKey = $this->generateCacheKey($httpRequest);
            $requestResponse = $this->cache->fetch($cacheKey);
        }

        if (!$requestResponse) {
            $requestResponse = $this->processRequest($httpRequest);
        }

        $json = $requestResponse->json();

        if (empty($json['status'])) {
            throw ExceptionFactory::missingKeyInResponse('status');
        }

        if ($json['status'] == 'error') {
            if (empty($json['error'])) {
                throw ExceptionFactory::missingKeyInResponse('error');
            }

            throw ExceptionFactory::requestErrorFromWarGamingResponse($json['error']);
        } else if ($json['status'] == 'ok') {
            if (empty($json['data'])) {
                throw ExceptionFactory::missingKeyInResponse('data');
            }

            if ($mustCache) {
                $this->cache->set($cacheKey, $requestResponse, $method->getCacheTtl());
            }

            $data = $json['data'];
        } else {
            throw ExceptionFactory::unavailableStatus($json['status']);
        }

        $response = $processor->parseResponse($data, $json, $requestResponse, $method);

        return $response;
    }

    /**
     * Process request
     *
     * @param RequestInterface $httpRequest
     *
     * @return \Guzzle\Http\Message\Response
     *
     * @throws \Exception
     */
    protected function processRequest(RequestInterface $httpRequest)
    {
        // Sending request
        try {
            $requestResponse = $this->httpClient->send($httpRequest);
            $this->dispatch(Events::REQUEST_COMPLETE, new Events\RequestCompleteEvent($httpRequest, $requestResponse));

        } catch (BadResponseException $e) {
            $this->dispatch(Events::REQUEST_ERROR, new Events\RequestErrorEvent($e));

            throw $e;
        }

        // Check of access denied
        if (403 == $requestResponse->getStatusCode()) {
            throw ExceptionFactory::httpAccessDenied();
        }

        // Check of page not found and method not allowed
        if (404 == $requestResponse->getStatusCode()) {
            throw ExceptionFactory::httpPageNotFound();
        }

        // Check of server error
        if (500 == $requestResponse->getStatusCode()) {
            throw ExceptionFactory::httpServerError();
        }

        if (!$requestResponse->isSuccessful()) {
            throw ExceptionFactory::httpError($requestResponse->getStatusCode());
        }

        return $requestResponse;
    }

    /**
     * Dispatch event
     *
     * @param string $name
     * @param Event $event
     */
    protected function dispatch($name, Event $event)
    {
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch($name, $event);
        }
    }

    /**
     * Create processor
     *
     * @param MethodInterface $method
     *
     * @return \WarGaming\Api\Method\ProcessorInterface
     *
     * @throws \RuntimeException
     */
    protected function createProcessor(MethodInterface $method)
    {
        $processorClass = $method->getProcessorClass();

        if (!class_exists($processorClass)) {
            throw new \RuntimeException(sprintf(
                'Not found process class "%s".',
                $processorClass
            ));
        }

        $processorInstance = new $processorClass();

        if (!$processorInstance instanceof ProcessorInterface) {
            throw new \RuntimeException(sprintf(
                'Process must be implements of ProcessorInterface, "%s" given.',
                get_class($processorInstance)
            ));
        }

        $processorInstance->setFormDataGenerator($this->formDataGenerator);
        $processorInstance->setValidator($this->validator);

        return $processorInstance;
    }

    /**
     * Create default client
     *
     * @param Reader $reader
     *
     * @return Client
     */
    public static function createDefault(Reader $reader = null)
    {
        if (!$reader) {
            $reader = new AnnotationReader();
        }

        $validatorBuilder = new ValidatorBuilder();
        $validatorBuilder->enableAnnotationMapping($reader);

        $guzzle = new GuzzleClient();
        $validator = $validatorBuilder->getValidator();
        $eventDispatcher = new EventDispatcher();
        $formDataGenerator = new FormDataGenerator($reader);

        /** @var Client $client */
        $client = new static($guzzle, $validator, $eventDispatcher, $formDataGenerator);
        $client->setCache(new ArrayCache());

        return $client;
    }

    /**
     * Generate cache key via request
     *
     * @param RequestInterface $request
     *
     * @return string
     */
    private function generateCacheKey(RequestInterface $request)
    {
        $contentBody = null;

        if ($request instanceof EntityEnclosingRequestInterface) {
            $contentBody = $request->getBody();
        }

        $payload = sprintf(
            '%s%s%s%s',
            $request->getMethod(),
            $request->getUrl(),
            $request->getQuery(),
            $contentBody
        );

        return md5($payload);
    }
}
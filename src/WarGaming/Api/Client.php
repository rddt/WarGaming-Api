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
use Guzzle\Http\Message\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;
use WarGaming\Api\Cache\ArrayCache;
use WarGaming\Api\Cache\CacheInterface;
use WarGaming\Api\Events\MethodCompleteEvent;
use WarGaming\Api\Events\MethodErrorEvent;
use WarGaming\Api\Events\MethodStartEvent;
use WarGaming\Api\Exception\ExceptionFactory;
use WarGaming\Api\Exception\RequestErrorException;
use WarGaming\Api\Factory\ApplicationIdFactoryInterface;
use WarGaming\Api\Factory\NativeApplicationIdFactory;
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
     * @var ApplicationIdFactoryInterface
     */
    private $applicationIdFactory;

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
     * Suppress source not available error
     *
     * @var int
     */
    private $suppressSourceNotAvailableError = 3;

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
    ) {
        $this->httpClient = $httpClient;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
        $this->formDataGenerator = $formDataGenerator;
        $this->apiHost = $apiHost ?: self::DEFAULT_HOST;
        $this->apiSecure = (bool) $apiSecure;
        $this->cache = new ArrayCache();
    }

    /**
     * Set suppress source not available error
     *
     * @param int $count
     *
     * @return Client
     */
    public function setSuppressSourceNotAvailableError($count)
    {
        $this->suppressSourceNotAvailableError = $count;

        return $this;
    }

    /**
     * Set application ID
     *
     * @param string|object $applicationId
     *
     * @return Client
     *
     * @throws \InvalidArgumentException
     */
    public function setApplicationId($applicationId)
    {
        if (is_scalar($applicationId)) {
            $applicationId = new NativeApplicationIdFactory($applicationId);
        } elseif (!$applicationId instanceof ApplicationIdFactoryInterface) {
            throw new \InvalidArgumentException(sprintf(
                'The first parameter must be implements of ApplicationIdFactoryInterface, but "%s" given.',
                get_class($applicationId)
            ));
        }

        $this->applicationIdFactory = $applicationId;

        return $this;
    }

    /**
     * Get application id factory
     *
     * @return ApplicationIdFactoryInterface
     */
    public function getApplicationIdFactory()
    {
        return $this->applicationIdFactory;
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
        try {
            $this->dispatch(Events::METHOD_START, new MethodStartEvent($method));
            $response = $this->doRequest($method);
            $this->dispatch(Events::METHOD_COMPLETE, new MethodCompleteEvent($method, $response));

        } catch (\Exception $e) {
            $this->dispatch(Events::METHOD_ERROR, new MethodErrorEvent($method, $e));

            throw $e;
        }

        return $response;
    }

    /**
     * API Request process
     *
     * @param MethodInterface $method
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function doRequest(MethodInterface $method)
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

        // Set application ID to request
        if (!$this->applicationIdFactory) {
            throw ExceptionFactory::missingApplicationId();
        }

        // Start request. Get application ID
        $applicationId = $this->applicationIdFactory->getApplicationId();

        $httpRequest->getQuery()->add('application_id', $applicationId);

        // Check request in cache storage
        $requestResponse = null;
        $json = null;
        $cacheKey = null;
        $mustCache = $method->isCacheAvailable() && null !== $ttl = $method->getCacheTtl() && $this->cache;

        if ($mustCache) {
            // Try get response from cache storage
            $cacheKey = $this->generateCacheKey($httpRequest);
            if ($requestResponse = $this->cache->fetch($cacheKey)) {
                $json = $this->processResponse($requestResponse);
            }
        }

        if (!$requestResponse) {
            $tryIteration = $this->suppressSourceNotAvailableError > 0 ?: 1;
            $lastException = null;

            while ($tryIteration--) {
                try {
                    // Process request
                    $requestResponse = $this->processRequest($httpRequest);
                    $this->applicationIdFactory->process($applicationId);

                    // Process response
                    $json = $this->processResponse($requestResponse);

                    if ($mustCache) {
                        // Response must be caching
                        $this->cache->set(
                            $this->generateCacheKey($httpRequest),
                            $requestResponse,
                            $method->getCacheTtl()
                        );
                    }

                    $this->dispatch(Events::REQUEST_COMPLETE, new Events\RequestCompleteEvent($httpRequest, $requestResponse));

                    // All OK. Exit from loop.
                    if ($lastException) {
                        $lastException = null;
                    }
                    break;
                } catch (\Exception $e) {
                    $lastException = $e;

                    $this->applicationIdFactory->process($applicationId);
                    $this->dispatch(Events::REQUEST_ERROR, new Events\RequestErrorEvent($e));

                    if ($e instanceof RequestErrorException) {
                        if ($e->getCode() === RequestErrorException::SOURCE_NOT_AVAILABLE) {
                            continue;
                        }
                    }

                    throw $e;
                }
            }

            if ($lastException) {
                throw $lastException;
            }
        }

        $data = $json['data'];

        $response = $processor->parseResponse($data, $json, $requestResponse, $method);

        return $response;
    }

    /**
     * Process request
     *
     * @param RequestInterface $httpRequest
     *
     * @return Response
     *
     * @throws \Exception
     */
    protected function processRequest(RequestInterface $httpRequest)
    {
        // Call request start event.
        // Must be call before set authorization info
        $this->dispatch(Events::REQUEST_START, new Events\RequestStartEvent($httpRequest));

        // Sending request
        try {
            $requestResponse = $this->httpClient->send($httpRequest);

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
     * Process response
     *
     * @param Response $response
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function processResponse(Response $response)
    {
        $json = $response->json();

        if (empty($json['status'])) {
            throw ExceptionFactory::missingKeyInResponse('status');
        }

        if ($json['status'] == 'error') {
            if (empty($json['error'])) {
                throw ExceptionFactory::missingKeyInResponse('error');
            }

            throw ExceptionFactory::requestErrorFromWarGamingResponse($json['error']);
        } elseif ($json['status'] == 'ok') {
            if (!isset($json['data'])) {
                throw ExceptionFactory::missingKeyInResponse('data');
            }
        } else {
            throw ExceptionFactory::unavailableStatus($json['status']);
        }

        return $json;
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

        return $client;
    }

    /**
     * Generate cache key via request
     *
     * @todo: can modify hash via lifetime? Because if one request has lifetime 1 hour,
     * and another similar requests have 2 hour.
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

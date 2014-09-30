<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Method;

use Guzzle\Http\Client as GuzzleClient;
use WarGaming\Api\FormData\FormDataGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Abstract processor system
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
abstract class AbstractProcessor implements ProcessorInterface
{
    /**
     * @var FormDataGeneratorInterface
     */
    protected $formDataGenerator;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * {@inheritDoc}
     */
    public function isReturnFalse404()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function setFormDataGenerator(FormDataGeneratorInterface $formDataGenerator)
    {
        $this->formDataGenerator = $formDataGenerator;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function createRequest(GuzzleClient $httpClient, MethodInterface $method)
    {
        $url = '/' . trim($this->getApiUri(), '/') . '/';
        $request = $httpClient->createRequest($this->getApiRequestMethod(), $url);

        // Try generate form data for request.
        $formData = $this->getApiRequestData($method);

        if (count($formData)) {
            // Form data exists. Set to query
            $query = $request->getQuery();
            foreach ($formData as $key => $value) {
                $query->set($key, $value);
            }
        }

        return $request;
    }

    /**
     * Get API Uri
     *
     * @return string
     */
    abstract protected function getApiUri();

    /**
     * Get API request method
     *
     * @return string
     */
    protected function getApiRequestMethod()
    {
        return 'GET';
    }

    /**
     * Generate API request data
     *
     * @param MethodInterface $method
     *
     * @return array
     */
    protected function getApiRequestData(MethodInterface $method)
    {
        return $this->formDataGenerator->getData($method);
    }
}
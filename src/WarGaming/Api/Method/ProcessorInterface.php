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
use Guzzle\Http\Message\Response as GuzzleResponse;
use WarGaming\Api\FormData\FormDataGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * All processors should be implement this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ProcessorInterface
{
    /**
     * Create a http request instance
     *
     * @param GuzzleClient      $httpClient
     * @param MethodInterface   $method
     *
     * @return \Guzzle\Http\Message\RequestInterface
     */
    public function createRequest(GuzzleClient $httpClient, MethodInterface $method);

    /**
     * Parse response
     *
     * @param array           $data
     * @param array           $fullData
     * @param GuzzleResponse  $response
     * @param MethodInterface $method
     *
     * @return mixed
     *
     * @throws \Exception   If data is invalid or error exists
     */
    public function parseResponse(array $data, array $fullData, GuzzleResponse $response, MethodInterface $method);

    /**
     * Is return false if page not found error (404)
     *
     * @return bool
     */
    public function isReturnFalse404();

    /**
     * Set form data generator
     *
     * @param FormDataGeneratorInterface $formDataGenerator
     */
    public function setFormDataGenerator(FormDataGeneratorInterface $formDataGenerator);

    /**
     * Set validator
     *
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator);
}

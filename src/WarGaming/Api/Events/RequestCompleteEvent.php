<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Events;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event for request complete
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class RequestCompleteEvent extends Event
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * Construct
     *
     * @param RequestInterface $request
     * @param Response $response
     */
    public function __construct(RequestInterface $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
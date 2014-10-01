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
use Symfony\Component\EventDispatcher\Event;

/**
 * Event for request start
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class RequestStartEvent extends Event
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Construct
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
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
}

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

use Symfony\Component\EventDispatcher\Event;
use WarGaming\Api\Method\MethodInterface;

/**
 * Event for call API method complete
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class MethodCompleteEvent extends Event
{
    /**
     * @var MethodInterface
     */
    private $method;

    /**
     * @var mixed
     */
    private $response;

    /**
     * Construct
     *
     * @param MethodInterface $method
     * @param mixed           $response
     */
    public function __construct(MethodInterface $method, $response)
    {
        $this->method = $method;
        $this->response = $response;
    }

    /**
     * Get method
     *
     * @return MethodInterface
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}

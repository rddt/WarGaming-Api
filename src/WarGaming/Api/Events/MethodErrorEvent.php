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
 * Event for call API method error
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class MethodErrorEvent extends Event
{
    /**
     * @var MethodInterface
     */
    private $method;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * Construct
     *
     * @param MethodInterface $method
     * @param \Exception      $exception
     */
    public function __construct(MethodInterface $method, \Exception $exception)
    {
        $this->method = $method;
        $this->exception = $exception;
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
     * Get exception
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}

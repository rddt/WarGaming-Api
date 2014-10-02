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

/**
 * Event for request error
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class RequestErrorEvent extends Event
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * Construct
     *
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
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

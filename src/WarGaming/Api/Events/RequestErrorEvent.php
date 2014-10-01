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

use Guzzle\Http\Exception\BadResponseException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event for request error
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class RequestErrorEvent extends Event
{
    /**
     * @var BadResponseException
     */
    private $exception;

    /**
     * Construct
     *
     * @param BadResponseException $exception
     */
    public function __construct(BadResponseException $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Get exception
     *
     * @return BadResponseException
     */
    public function getException()
    {
        return $this->exception;
    }
}

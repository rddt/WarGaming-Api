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
 * Event for call API method start
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class MethodStartEvent extends Event
{
    /**
     * @var MethodInterface
     */
    private $method;

    /**
     * Construct
     *
     * @param MethodInterface $method
     */
    public function __construct(MethodInterface $method)
    {
        $this->method = $method;
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
}

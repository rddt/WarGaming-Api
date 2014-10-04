<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Factory;

/**
 * Native application id factory
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class NativeApplicationIdFactory implements ApplicationIdFactoryInterface
{
    /**
     * @var string
     */
    private $applicationId;

    /**
     * Construct
     *
     * @param string $applicationId
     */
    public function __construct($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * {@inheritDoc}
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * {@inheritDoc}
     */
    public function process($applicationId)
    {
        // Nothing action
    }
}

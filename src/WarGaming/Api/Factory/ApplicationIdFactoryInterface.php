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
 * All application id factory should be implements of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ApplicationIdFactoryInterface
{
    /**
     * Get application ID
     *
     * @return string Application ID
     */
    public function getApplicationId();

    /**
     * Start request with application ID
     *
     * @param string $applicationId
     */
    public function process($applicationId);
}
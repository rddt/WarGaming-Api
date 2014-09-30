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

/**
 * All methods should be implement this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface MethodInterface
{
    /**
     * Get process class
     *
     * @return string
     */
    public function getProcessorClass();

    /**
     * Get groups validation
     *
     * @return array
     */
    public function getValidationGroups();

    /**
     * Get locale
     *
     * @return string
     */
    public function getLanguage();

    /**
     * Get cache ttl
     *
     * @return int|null
     */
    public function getCacheTtl();

    /**
     * Is cache available
     *
     * @return bool
     */
    public function isCacheAvailable();
}
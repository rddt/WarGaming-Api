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
 * Abstract API method
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
abstract class AbstractMethod implements MethodInterface
{
    /**
     * @var string
     */
    public $language;

    /**
     * @var int
     */
    public $cacheTtl;

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get processor class
     *
     * @return string
     */
    public function getProcessorClass()
    {
        return get_class($this) . 'Processor';
    }

    /**
     * Get group validations
     *
     * @return array
     */
    public function getValidationGroups()
    {
        return array('Default');
    }

    /**
     * Get cache ttl
     *
     * @return int|null
     */
    public function getCacheTtl()
    {
        return $this->cacheTtl;
    }

    /**
     * Is cache available
     *
     * @return bool
     */
    public function isCacheAvailable()
    {
        return false;
    }
}

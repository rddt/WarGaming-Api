<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Cache;

/**
 * All cache instance must be implement of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface CacheInterface
{
    /**
     * Set cache data
     *
     * @param string  $key
     * @param mixed   $data
     * @param integer $lifetime
     *
     * @return bool
     */
    public function set($key, $data, $lifetime = 0);

    /**
     * Fetch cache data
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch($key);

    /**
     * Has cache data in storage
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Remove cache data from storage
     *
     * @param string $key
     *
     * @return bool
     */
    public function remove($key);

    /**
     * Flush storage
     */
    public function flush();
}

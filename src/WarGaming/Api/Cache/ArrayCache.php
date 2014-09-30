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
 * Base array cache
 *
 * @author Vitaiy Zhuk <zhuk2205@gmail.com>
 */
class ArrayCache implements CacheInterface
{
    /**
     * @var array
     */
    private $storage;

    /**
     * {@inheritDoc}
     */
    public function set($key, $data, $lifetime = 0)
    {
        $this->storage[$key] = array(
            'data' => $data,
            'lifetime' => $lifetime
        );

        return true;
    }

    /**
     * Fetch cache data
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch($key)
    {
        if (!isset($this->storage[$key])) {
            return null;
        }

        $cache = $this->storage[$key];

        if ($cache['lifetime'] && $cache['lifetime'] < time()) {
            unset ($this->storage[$key]);

            return null;
        }

        return $cache['data'];
    }

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        return isset($this->storage[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        unset ($this->storage[$key]);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $this->storage = array();
    }
}

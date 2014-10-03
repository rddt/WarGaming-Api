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
 * Chain cache.
 * Attention: in fetch with key, load data will be saved to another caches in chain
 * with ttl - ChainCache::defaultFetchTtl.
 * For correct use, please use minimum ttl or use ArrayCache, which will be destroyed
 * on end script.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ChainCache implements CacheInterface
{
    /**
     * @var array
     */
    private $caches;

    /**
     * @var array|CacheInterface[]
     */
    private $sortedCaches;

    /**
     * @var array
     */
    private $notExists = array();

    /**
     * @var int
     */
    private $defaultFetchTtl;

    /**
     * Construct
     *
     * @param array|CacheInterface[] $caches
     * @param int                    $defaultFetchTtl
     */
    public function __construct(array $caches = array(), $defaultFetchTtl = 0)
    {
        foreach ($caches as $cache) {
            $this->add($cache);
        }

        $this->defaultFetchTtl = $defaultFetchTtl;
    }

    /**
     * Add cache to chain
     *
     * @param CacheInterface $cache
     * @param integer        $priority
     *
     * @return ChainCache
     */
    public function add(CacheInterface $cache, $priority = 0)
    {
        $this->caches[spl_object_hash($cache)] = array(
            'cache' => $cache,
            'priority' => $priority
        );

        $this->sortedCaches = null;

        return $this;
    }

    /**
     * {@inheritDoc}}
     */
    public function set($key, $data, $lifetime = 0)
    {
        foreach ($this->sortCaches() as $cache) {
            $cache->set($key, $data, $lifetime);
        }

        unset ($this->notExists[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($key)
    {
        if (isset($this->notExists[$key])) {
            return null;
        }

        /** @var CacheInterface[] $notExistsInCaches */
        $notExistsInCaches = array();

        $data = null;

        foreach ($this->sortCaches() as $cache) {
            $data = $cache->fetch($key);

            if (null === $data) {
                $notExistsInCaches[] = $cache;
            } elseif (null !== $data) {
                break;
            }
        }

        if (null === $data) {
            $this->notExists[$key] = true;
            return null;
        }

        foreach ($notExistsInCaches as $cache) {
            $cache->set($key, $data, $this->defaultFetchTtl);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        return (bool) $this->fetch($key);
    }

    /**
     * {@inheritDoc]
     */
    public function remove($key)
    {
        $this->notExists[$key] = true;

        foreach ($this->sortCaches() as $cache) {
            $cache->remove($key);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $this->notExists = array();

        foreach ($this->sortCaches() as $cache) {
            $cache->flush();
        }
    }

    /**
     * Sort caches
     *
     * @return array|CacheInterface[]
     */
    private function sortCaches()
    {
        if (null === $this->sortedCaches) {
            uasort($this->caches, function ($a, $b){
                if ($a['priority'] == $b['priority']) {
                    return 0;
                }

                return $a['priority'] < $b['priority'] ? 1 : -1;
            });

            $this->sortedCaches = array();

            foreach ($this->caches as $cache) {
                $this->sortedCaches[] = $cache['cache'];
            }
        }

        return $this->sortedCaches;
    }
}
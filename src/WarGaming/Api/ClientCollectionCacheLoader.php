<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api;

use Doctrine\Common\Annotations\Reader;
use WarGaming\Api\Cache\ArrayCache;
use WarGaming\Api\Cache\CacheInterface;
use WarGaming\Api\Method\MethodInterface;
use WarGaming\Api\Model\Collection;
use WarGaming\Api\Util\ReflectionHelper;

/**
 * Client collection cache loader
 *
 * Attention: now caching only "last" object, when not modified with another methods!
 * @todo: add system for control changed field for object, and save/fetch only values
 * for changed properties of object.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ClientCollectionCacheLoader
{
    /**
     * @var ClientCollectionLoader
     */
    private $collectionLoader;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * Construct
     *
     * @param ClientCollectionLoader $collectionLoader
     * @param Reader                 $reader
     * @param CacheInterface         $cache
     */
    public function __construct(ClientCollectionLoader $collectionLoader, Reader $reader = null, CacheInterface $cache = null)
    {
        $this->collectionLoader = $collectionLoader;
        $this->reader = $reader;
        $this->cache = $cache ?: new ArrayCache();
    }

    /**
     * Set cache storage
     *
     * @param CacheInterface $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Request
     *
     * @param MethodInterface $method
     *
     * @throws \RuntimeException
     */
    public function request(MethodInterface $method)
    {
        if (null === $cacheTtl = $method->getCacheTtl()) {
            throw new \RuntimeException('Can not use collection cache loader with empty cache ttl!');
        }

        $reflectionProperty = $this->getPropertyForCollectionLoad($method);

        if (!$reflectionProperty->isPublic()) {
            $reflectionProperty->setAccessible(true);
        }

        $collection = $reflectionProperty->getValue($method);

        if (!$collection instanceof Collection) {
            throw new \RuntimeException(sprintf(
                'The property value "%s" for method "%s" must be Collection instance, but "%s" given.',
                $reflectionProperty->getName(),
                get_class($method),
                is_object($collection) ? get_class($collection) : gettype($collection)
            ));
        }

        $forLoads = new Collection();
        $forLoadsIndexes = array();
        $uniqueIdentifiers = array();

        // First step: check collection item in cache storage.
        foreach ($collection as $index => $collectionItem) {
            if (!is_object($collectionItem)) {
                throw new \RuntimeException(sprintf(
                   'The object at index "%s" in request collection must be a object, but "%s" given.',
                   $index,
                   is_object($collectionItem) ? get_class($collectionItem) : gettype($collectionItem)
                ));
            }

            $identifier = ReflectionHelper::getIdentifierValue($this->reader, $collectionItem);

            if (null === $identifier) {
                throw new \RuntimeException(sprintf(
                    'Not found identifier for object "%s" at index "%s".',
                    get_class($collectionItem),
                    $index
                ));
            }

            $cacheKey = $this->generateCacheKey($method, $identifier);

            if ($data = $this->cache->fetch($cacheKey)) {
                ReflectionHelper::mergeObject($collection[$index], $data);
            } else {
                $forLoadsIndexes[$index] = $identifier;

                if (!in_array($identifier, $uniqueIdentifiers)) {
                    $forLoads[$index] = $collectionItem;
                }
            }

            $uniqueIdentifiers[] = $identifier;
        }

        if (!count($forLoads)) {
            return;
        }

        // Second step: set values for next load to method property and call to API client
        $reflectionProperty->setValue($method, $forLoads);

        // Attention: method for load collection could not return value.
        // All data must be saves in method property instance!
        $this->collectionLoader->request($method);

        // Third step: get identifiers from loads data
        $loadsIdentifiers = array();
        foreach ($forLoads as $index => $item) {
            if (!is_object($item)) {
                throw new \RuntimeException(sprintf(
                    'The loads value at index "%s" must be a object, but "%s" given.',
                    $index,
                    gettype($item)
                ));
            }

            $identifier = ReflectionHelper::getIdentifierValue($this->reader, $item);
            $loadsIdentifiers[$identifier] = $index;
        }

        // Fourth three: save values in storage
        $saves = array();
        foreach ($forLoadsIndexes as $index => $identifier) {
            $loadIndex = $loadsIdentifiers[$identifier];

            if (isset($forLoads[$loadIndex])) {
                $collectionItem = clone $forLoads[$loadIndex];
                $cacheKey = $this->generateCacheKey($method, $identifier);

                if (!in_array($identifier, $saves)) {
                    $this->cache->set($cacheKey, $collectionItem, $cacheTtl);
                    $saves[] = $identifier;
                }

                ReflectionHelper::mergeObject($collection[$index], $collectionItem);
            }
        }

        return;
    }

    /**
     * Generate cache key for method and index
     *
     * @param MethodInterface $method
     * @param string|int      $index
     *
     * @return string
     */
    private function generateCacheKey(MethodInterface $method, $index)
    {
        return get_class($method) . ':' . $index;
    }

    /**
     * Get property for collection load
     *
     * @param MethodInterface $method
     *
     * @return \ReflectionProperty
     *
     * @throws \RuntimeException
     */
    private function getPropertyForCollectionLoad(MethodInterface $method)
    {
        $methodReflection = new \ReflectionObject($method);
        $properties = ReflectionHelper::getProperties($methodReflection);

        $availableProperties = array();

        foreach ($properties as $property) {
            /** @var \WarGaming\Api\Annotation\FormData $annotation */
            $annotation = $this->reader->getPropertyAnnotation($property, 'WarGaming\Api\Annotation\FormData');

            if ($annotation && $annotation->collectionLoad && $annotation->collectionCacheLoad) {
                $availableProperties[] = $property;
            }
        }

        if (count($availableProperties) > 1) {
            throw new \RuntimeException(sprintf(
                'Many properties for collection cache loads in method "%s". Must be one collection.',
                get_class($method)
            ));
        }

        if (!count($availableProperties)) {
            throw new \RuntimeException(sprintf(
                'Not found property for collection cache loads in method "%s".',
                get_class($method)
            ));
        }

        return $availableProperties[0];
    }
}
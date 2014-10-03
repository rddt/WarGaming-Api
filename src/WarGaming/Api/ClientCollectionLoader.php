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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use WarGaming\Api\Method\MethodInterface;
use WarGaming\Api\Model\Collection;
use WarGaming\Api\Util\ReflectionHelper;

/**
 * Collection loader
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ClientCollectionLoader
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * Construct
     *
     * @param Client $client
     * @param Reader $reader
     */
    public function __construct(Client $client, Reader $reader = null)
    {
        $this->client = $client;
        $this->reader = $reader ?: new AnnotationReader();
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
        // Get reflection property from method
        $propertyReflection = $this->getPropertyForCollectionLoad($method);

        // Get count constraint for get max count
        /** @var \Symfony\Component\Validator\Constraints\Count $countAnnotation */
        $countAnnotation = $this->reader->getPropertyAnnotation(
            $propertyReflection,
            'Symfony\Component\Validator\Constraints\Count'
        );

        if (!$countAnnotation || !$countAnnotation->max) {
            // Not found count constraint. Use default.
            $this->client->request($method);
        }

        if (!$propertyReflection->isPublic()) {
            $propertyReflection->setAccessible(true);
        }

        $collection = $propertyReflection->getValue($method);

        if (!$collection instanceof Collection) {
            throw new \RuntimeException(sprintf(
                'The value for property "%s" must be Collection instance, but "%s" given.',
                is_object($collection) ? get_class($collection) : gettype($collection)
            ));
        }

        if (count($collection) <= $countAnnotation->max) {
            // Not exceeded the limit. Use default.
            $this->client->request($method);
        }

        // Limit exceeded. Grouping.
        $groupValues = new Collection();
        $groups = array();
        $countInGroup = 0;

        foreach ($collection as $index => $value) {
            $groupValues[$index] = $value;
            $countInGroup++;

            if ($countInGroup >= $countAnnotation->max) {
                $countInGroup = 0;
                $groups[] = $groupValues;
                $groupValues = new Collection();
            }
        }

        if (count($groupValues)) {
            $groups[] = $groupValues;
        }

        $collection->clear();

        foreach ($groups as $group) {
            $propertyReflection->setValue($method, $group);
            $this->client->request($method);

            $collection->addCollection($group);
        }
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

            if ($annotation && $annotation->collectionLoad) {
                $availableProperties[] = $property;
            }
        }

        if (count($availableProperties) > 1) {
            throw new \RuntimeException(sprintf(
                'Many properties for collection loads in method "%s". Must be one collection.',
                get_class($method)
            ));
        }

        if (!count($availableProperties)) {
            throw new \RuntimeException(sprintf(
                'Not found property for collection loads in method "%s".',
                get_class($method)
            ));
        }

        return $availableProperties[0];
    }
}

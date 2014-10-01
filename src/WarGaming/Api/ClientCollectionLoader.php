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
     * @return array
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
            return $this->client->request($method);
        }

        if (!$propertyReflection->isPublic()) {
            $propertyReflection->setAccessible(true);
        }

        $values = $propertyReflection->getValue($method);

        if (count($values) <= $countAnnotation->max) {
            // Not exceeded the limit. Use default.
            return $this->client->request($method);
        }

        // Limit exceeded. Grouping.
        $groupValues = array();
        $groups = array();
        $countInGroup = 0;

        foreach ($values as $index => $value) {
            $groupValues[$index] = $value;
            $countInGroup++;

            if ($countInGroup >= $countAnnotation->max) {
                $countInGroup = 0;
                $groups[] = $groupValues;
                $groupValues = array();
            }
        }

        if (count($groupValues)) {
            $groups[] = $groupValues;
        }

        // Group iteration
        $result = array();
        foreach ($groups as $group) {
            $propertyReflection->setValue($method, $group);

            $data = $this->client->request($method);

            if (!is_array($data)) {
                throw new \RuntimeException(sprintf(
                    'The processor class for method "%s" must be return array, but "%s" given.',
                    get_class($method),
                    is_object($data) ? get_class($data) : gettype($data)
                ));
            }

            $result = array_merge($result, $data);
        }

        return $result;
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
    public function getPropertyForCollectionLoad(MethodInterface $method)
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

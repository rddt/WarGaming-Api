<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Util;
use Doctrine\Common\Annotations\Reader;

/**
 * Reflection helper
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ReflectionHelper
{
    /**
     * @var array|\ReflectionProperty[]
     */
    private static $identifierPropertyReflections = array();

    /**
     * Get properties from reflection class or object
     *
     * @param \ReflectionObject|\ReflectionClass $reflection
     *
     * @return array|\ReflectionProperty[]
     *
     * @throws \InvalidArgumentException
     */
    public static function getProperties($reflection)
    {
        if (!$reflection instanceof \ReflectionClass && !$reflection instanceof \ReflectionObject) {
            throw new \InvalidArgumentException(sprintf(
                'The first parameters must be a instance of ReflectionClass or ReflectionObject, but "%s" given.',
                is_object($reflection) ? get_class($reflection) : gettype($reflection)
            ));
        }

        $properties = $reflection->getProperties();

        while ($reflection = $reflection->getParentClass()) {
            $properties = array_merge($properties, $reflection->getProperties());
        }

        return $properties;
    }

    /**
     * Get property by name
     *
     * @param \ReflectionObject|\ReflectionClass $reflection
     * @param string                             $propertyName
     *
     * @return \ReflectionProperty
     */
    public static function getPropertyByName($reflection, $propertyName)
    {
        foreach (self::getProperties($reflection) as $property) {
            if ($property->getName() == $propertyName) {
                return $property;
            }
        }

        return null;
    }

    /**
     * Get identifier value from object
     *
     * @param Reader $reader
     * @param object $object
     *
     * @return string|integer|null
     */
    public static function getIdentifierValue(Reader $reader, $object)
    {
        $class = get_class($object);

        if (!isset(self::$identifierPropertyReflections[$class])) {
            self::$identifierPropertyReflections[$class] = null;

            $reflection = new \ReflectionObject($object);
            $properties = ReflectionHelper::getProperties($reflection);

            foreach ($properties as $property) {
                $annotation = $reader->getPropertyAnnotation($property, 'WarGaming\Api\Annotation\Id');

                if ($annotation) {
                    // Annotation exists. This is a identifier value
                    if (!$property->isPublic()) {
                        $property->setAccessible(true);
                    }

                    self::$identifierPropertyReflections[$class] = $property;

                    return $property->getValue($object);
                }
            }
        }

        if (!empty(self::$identifierPropertyReflections[$class])) {
            return self::$identifierPropertyReflections[$class]->getValue($object);
        }

        return null;
    }

    /**
     * Merge objects
     *
     * @param object $target
     * @param object $source
     *
     * @throws \InvalidArgumentException
     */
    public static function mergeObject($target, $source)
    {
        if (get_class($target) != get_class($source)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid source object. Target object: "%s"; Source object: "%s"',
                get_class($target),
                get_class($source)
            ));
        }

        foreach ($source as $key => $value) {
            $target->{$key} = $value;
        }
    }
}

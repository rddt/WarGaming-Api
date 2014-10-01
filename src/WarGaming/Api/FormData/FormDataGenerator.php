<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\FormData;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\Collection;

/**
 * Form data generator
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class FormDataGenerator implements FormDataGeneratorInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * Construct
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Generate data from model
     *
     * @param object $model
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getData($model)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException(sprintf(
                'The model must be a object, "%s" given.',
                gettype($model)
            ));
        }

        $reflectionModel = new \ReflectionObject($model);

        $properties = $this->getProperties($reflectionModel);

        $formData = array();

        foreach ($properties as $property) {
            /** @var \WarGaming\Api\Annotation\FormData $annotation */
            $annotation = $this->reader->getPropertyAnnotation($property, 'WarGaming\Api\Annotation\FormData');

            if ($annotation) {
                // Form data annotation exists.
                $name = $annotation->name ? $annotation->name : $property->getName();
                $type = $annotation->type;

                if (!$property->isPublic()) {
                    $property->setAccessible(true);
                }

                $value = $property->getValue($model);

                switch ($type) {
                    case 'list':
                        if (!is_array($value) && !$value instanceof \Traversable) {
                            $value = array($value);
                        }

                        $listValue = array();
                        foreach ($value as $element) {
                            if (is_object($element)) {
                                // Try get value from object by identifier.
                                $element = $this->getIdentifierValue($element);
                            }

                            if ($element) {
                                $listValue[] = $element;
                            }
                        }

                        $value = implode(',', $listValue);

                        break;
                }

                $formData[$name] = $value;
            }
        }

        return $formData;
    }

    /**
     * Get properties from model
     *
     * @param \ReflectionObject $object
     *
     * @return array|\ReflectionProperty[]
     */
    private function getProperties(\ReflectionObject $object)
    {
        $properties = $object->getProperties();

        while ($object = $object->getParentClass()) {
            $properties = array_merge($properties, $object->getProperties());
        }

        return $properties;
    }

    /**
     * Get identifier value from object
     *
     * @param object $object
     * @return string|integer|null
     */
    private function getIdentifierValue($object)
    {
        $reflection = new \ReflectionObject($object);
        $properties = $this->getProperties($reflection);

        foreach ($properties as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, 'WarGaming\Api\Annotation\Id');

            if ($annotation) {
                // Annotation exists. This is a identifier value
                if (!$property->isPublic()) {
                    $property->setAccessible(true);
                }

                return $property->getValue($object);
            }
        }

        return null;
    }
}

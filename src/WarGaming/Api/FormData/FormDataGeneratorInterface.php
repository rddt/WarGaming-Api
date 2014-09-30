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

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface FormDataGeneratorInterface
{
    /**
     * Get data as array from object model
     *
     * @param object $model
     * @return array
     */
    public function getData($model);
}
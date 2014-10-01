<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Model\WoT;

/**
 * Province model
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Province
{
    /**
     * @var string
     */
    public $id;

    /**
     * Create new province from array
     *
     * @param array $data
     *
     * @return Province
     */
    public static function createFromArray(array $data)
    {
        /** @var Province $province */
        $province = new static();

        $province->id = isset($data['province_id']) ? $data['province_id'] : null;

        return $province;
    }
}
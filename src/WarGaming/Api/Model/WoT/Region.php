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
 * Region model
 *
 * @author inph
 */
class Region
{
    /**
     * @var string
     */
    public $id;

    /**
     * Create new region from array
     *
     * @param array $data
     *
     * @return Region
     */
    public static function createFromArray(array $data)
    {
        /** @var Region $region */
        $region = new static();

        $region->id = isset($data['region_id']) ? $data['region_id'] : null;
        $region->regionI18n = isset($data['region_i18n']) ? $data['region_i18n'] : null;

        return $region;
    }
}

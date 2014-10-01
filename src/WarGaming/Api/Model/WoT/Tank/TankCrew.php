<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Model\WoT\Tank;

/**
 * Tank crew
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TankCrew
{
    /**
     * @var string
     */
    public $role;

    /**
     * Create tank crew instance from array
     *
     * @param array $data
     *
     * @return TankCrew
     */
    public static function createFromArray(array $data)
    {
        /** @var TankCrew $crew */
        $crew = new static();

        $crew->role = isset($data['role']) ? $data['role'] : null;

        return $crew;
    }
}

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

use WarGaming\Api\Model\WoT\Tank\Module\Gun;

/**
 * Tank gun
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TankGun
{
    /**
     * @var Gun
     */
    public $gun;

    /**
     * @var bool
     */
    public $default;

    /**
     * Create tank gun from array
     *
     * @param array $data
     *
     * @return TankGun
     */
    public static function createFromArray(array $data)
    {
        /** @var TankGun $tankGun */
        $tankGun = new static();

        $tankGun->default = isset($data['is_default']) ? $data['is_default'] : null;

        if (!empty($data['module_id'])) {
            $gun = new Gun();
            $gun->id = $data['module_id'];
            $tankGun->gun = $gun;
        }

        return $tankGun;
    }
}

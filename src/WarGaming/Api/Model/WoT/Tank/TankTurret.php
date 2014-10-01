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

use WarGaming\Api\Model\WoT\Tank\Module\Turret;

/**
 * Tank turret module
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TankTurret
{
    /**
     * @var Turret
     */
    public $turret;

    /**
     * @var bool
     */
    public $default;

    /**
     * Create tank radio from array
     *
     * @param array $data
     *
     * @return TankRadio
     */
    public static function createFromArray(array $data)
    {
        /** @var TankTurret $tankTurret */
        $tankTurret = new static();

        $tankTurret->default = isset($data['is_default']) ? $data['is_default'] : null;

        if (!empty($data['module_id'])) {
            $turret = new Turret();
            $turret->id = $data['module_id'];
            $tankTurret->turret = $turret;
        }

        return $tankTurret;
    }
}

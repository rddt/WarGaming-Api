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

use WarGaming\Api\Model\WoT\Tank\Module\Radio;

/**
 * Tank radio module
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TankRadio
{
    /**
     * @var Radio
     */
    public $radio;

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
        /** @var TankRadio $tankRadio */
        $tankRadio = new static();

        $tankRadio->default = isset($data['is_default']) ? $data['is_default'] : null;

        if (!empty($data['module_id'])) {
            $radio = new Radio();
            $radio->id = $data['module_id'];
            $tankRadio->radio = $radio;
        }

        return $tankRadio;
    }
}

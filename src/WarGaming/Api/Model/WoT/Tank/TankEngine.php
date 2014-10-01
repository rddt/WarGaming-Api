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

use WarGaming\Api\Model\WoT\Tank\Module\Engine;

/**
 * Tank engine
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TankEngine
{
    /**
     * @var Engine
     */
    public $engine;

    /**
     * @var bool
     */
    public $default;

    /**
     * Create new tank engine from array
     *
     * @param array $data
     *
     * @return TankEngine
     */
    public static function createFromArray(array $data)
    {
        /** @var TankEngine $tankEngine */
        $tankEngine = new static();

        $tankEngine->default = isset($data['is_default']) ? $data['is_default'] : false;

        if (!empty($data['module_id'])) {
            $engine = new Engine();
            $engine->id = $data['module_id'];
            $tankEngine->engine = $engine;
        }

        return $tankEngine;
    }
}

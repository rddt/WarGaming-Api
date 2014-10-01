<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Method\WoT\GlobalWar;

use WarGaming\Api\Method\AbstractMethod;

/**
 * Get maps from global
 *
 * @author Vitaliy Zhuk
 */
class Maps extends AbstractMethod
{
    /**
     * @var int
     */
    public $cacheTtl = 3600;

    /**
     * {@inheritDoc}
     */
    public function isCacheAvailable()
    {
        return true;
    }
}

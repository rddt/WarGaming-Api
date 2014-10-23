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

use WarGaming\Api\Model\Collection;

/**
 * Region collection
 *
 * @author Mike Flisher <mike.flisher@gmail.com>
 */
class RegionCollection extends Collection
{
    /**
     * @var array|Region[]
     */
    protected $storage;

    /**
     * Get regions
     *
     * @return RegionCollection|Region[]
     */
    public function getRegions()
    {
        $regions = new RegionCollection();

        $regions->addCollection($this->storage);

        return $regions;
    }
}

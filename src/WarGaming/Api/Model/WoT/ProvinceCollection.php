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
 * Province collection
 *
 * @author inph
 */
class ProvinceCollection extends Collection
{
    /**
     * @var array|Province[]
     */
    protected $storage;

    /**
     * Get accounts
     *
     * @return ProvinceCollection|Neighbor[]
     */
    public function getNeighbors()
    {
        $neighbors = new ProvinceCollection();

        foreach ($this->storage as $province) {
            $provinces->addCollection($province->neighbors);
        }

        return $neighbors;
    }
}

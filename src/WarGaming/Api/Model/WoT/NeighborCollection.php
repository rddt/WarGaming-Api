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
 * Neighbor collection
 *
 * @author Mike Flisher <mike.flisher@gmail.com>
 */
class NeighborCollection extends Collection
{
    /**
     * @var array|Neighbor[]
     */
    protected $storage;

    /**
     * Get neighbors
     *
     * @return NeighborCollection|Neighbor[]
     */
    public function getNeighbors()
    {
        $neighbors = new NeighborCollection();

        $neighbors->addCollection($this->storage);

        return $neighbors;
    }
}

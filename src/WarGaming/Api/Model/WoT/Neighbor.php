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
 * Neighbor model
 *
 * @author inph
 */
class Neighbor
{
    /**
     * @var string
     */
    public $id;

    /**
     * Create new neighbor from array
     *
     * @param array $data
     *
     * @return Neighbor
     */
    public static function createFromArray(array $data)
    {
        /** @var Neighbor $neighbor */
        $neighbor = new static();

        $neighbor->id = isset($data['neighbor_id']) ? $data['neighbor_id'] : null;

        return $neighbor;
    }
}

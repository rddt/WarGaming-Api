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
 * Global map model
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class GlobalMap
{
    const STATE_UNDEFINED       = 0;
    const STATE_ACTIVE          = 1;
    const STATE_UNAVAILABLE     = 2;
    const STATE_FROZEN          = 3;

    const TYPE_UNDEFINED        = 0;
    const TYPE_DEFAULT          = 1;
    const TYPE_EVENT            = 2;

    /**
     * @var string
     */
    public $id;

    /**
     * @var integer
     */
    public $state = self::STATE_UNDEFINED;

    /**
     * @var integer
     */
    public $type = self::TYPE_UNDEFINED;

    /**
     * @var string
     */
    public $url;

    /**
     * Create instance from array
     *
     * @param array $data
     *
     * @return GlobalMap
     */
    public static function createFromArray(array $data)
    {
        /** @var GlobalMap $map */
        $map = new static();

        $map->id = isset($data['map_id']) ? $data['map_id'] : null;
        $map->url = isset($data['map_url']) ? $data['map_url'] : null;

        if (isset($data['state'])) {
            $states = array(
                'active' => self::STATE_ACTIVE,
                'unavailable' => self::STATE_UNAVAILABLE,
                'frozen' => self::STATE_FROZEN // Or another key?
            );

            if (isset($states[$data['state']])) {
                $map->state = $states[$data['state']];
            }
        }

        if (isset($data['type'])) {
            $types = array(
                'normal' => self::TYPE_DEFAULT,
                'event' => self::TYPE_EVENT
            );

            if (isset($types[$data['type']])) {
                $map->type = $types[$data['type']];
            }
        }

        return $map;
    }
}

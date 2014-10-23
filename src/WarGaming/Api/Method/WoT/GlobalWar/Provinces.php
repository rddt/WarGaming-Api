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

use Symfony\Component\Validator\Constraints as Assert;
use WarGaming\Api\Annotation\FormData;
use WarGaming\Api\Method\AbstractMethod;

/**
 * Get returns list of provinces on the selected Global map
 *
 * @author Mike Flisher <mike.flisher@gmail.com>
 */
class Provinces extends AbstractMethod
{
    /**
     * @var integer
     *
     * @Assert\NotBlank
     *
     * @FormData(name="map_id")
     */
    public $map;

    /**
     * @var int
     */
    public $cacheTtl = 7200;

    /**
     * {@inheritDoc}
     */
    public function isCacheAvailable()
    {
        return true;
    }
}

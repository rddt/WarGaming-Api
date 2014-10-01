<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Method\WoT\Encyclopedia;

use Symfony\Component\Validator\Constraints as Assert;
use WarGaming\Api\Annotation\FormData;
use WarGaming\Api\Method\AbstractMethod;

/**
 * Tank info API
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TankInfo extends AbstractMethod
{
    /**
     * @var array|\WarGaming\Api\Model\WoT\Tank\Tank[]
     *
     * @Assert\Type("array")
     * @Assert\Count(
     *      min = 1,
     *      max = 100
     * )
     * @Assert\All({
     *      @Assert\Type("WarGaming\Api\Model\WoT\Tank\Tank")
     * })
     *
     * @FormData(name="tank_id", type="list")
     */
    public $tanks = array();

    /**
     * @var int
     */
    public $cacheTtl = 86400; // 1 day

    /**
     * {@inheritDoc}
     */
    public function isCacheAvailable()
    {
        return true;
    }
}

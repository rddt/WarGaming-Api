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
use WarGaming\Api\Model\Collection;

/**
 * Tank info API
 *
 * Attention: the processor this this method not return value. All tank info
 * saved in TankInfo::tanks property!
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TankInfo extends AbstractMethod
{
    /**
     * @var Collection|\WarGaming\Api\Model\WoT\Tank\Tank[]
     *
     * @Assert\Type("WarGaming\Api\Model\Collection")
     * @Assert\Count(
     *      min = 1,
     *      max = 100
     * )
     * @Assert\All({
     *      @Assert\Type("WarGaming\Api\Model\WoT\Tank\Tank")
     * })
     *
     * @FormData(name="tank_id", type="list", collectionLoad=true, collectionCacheLoad=true)
     */
    public $tanks;

    /**
     * @var int
     */
    public $cacheTtl = 86400; // 1 day

    /**
     * Construct
     */
    public function __construct()
    {
        $this->tanks = new Collection();
    }

    /**
     * {@inheritDoc}
     */
    public function isCacheAvailable()
    {
        return true;
    }
}

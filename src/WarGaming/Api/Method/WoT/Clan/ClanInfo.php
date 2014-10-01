<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Method\WoT\Clan;

use Symfony\Component\Validator\Constraints as Assert;
use WarGaming\Api\Annotation\FormData;
use WarGaming\Api\Annotation\CollectionLoad;
use WarGaming\Api\Method\AbstractMethod;
use WarGaming\Api\Model\WoT\Clan;

/**
 * Clan info API
 */
class ClanInfo extends AbstractMethod
{
    /**
     * @var array|Clan[]
     *
     * @Assert\Count(
     *      min = 1,
     *      max = 100
     * )
     * @Assert\Type("array")
     * @Assert\All({
     *      @Assert\Type("WarGaming\Api\Model\WoT\Clan")
     * })
     *
     * @FormData(name="clan_id", type="list", collectionLoad=true)
     */
    public $clans = array();

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

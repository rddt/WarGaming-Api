<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Method\WoT\Account;

use Symfony\Component\Validator\Constraints as Assert;
use WarGaming\Api\Annotation\FormData;
use WarGaming\Api\Method\AbstractMethod;
use WarGaming\Api\Model\Collection;
use WarGaming\Api\Model\WoT\Account;

/**
 * Account tanks API
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Tanks extends AbstractMethod
{
    /**
     * @var Collection|Account[]
     *
     * @Assert\Type("WarGaming\Api\Model\Collection")
     * @Assert\Count(
     *      min = 1,
     *      max = 100
     * )
     * @Assert\All({
     *      @Assert\Type("WarGaming\Api\Model\WoT\Account")
     * })
     *
     * @FormData(name="account_id", type="list", collectionLoad=true)
     */
    public $accounts;

    /**
     * @var int
     */
    public $cacheTtl = 10800; // 3 hours

    /**
     * Construct
     */
    public function __construct()
    {
        $this->accounts = new Collection();
    }

    /**
     * {@inheritDoc}
     */
    public function isCacheAvailable()
    {
        return true;
    }
}

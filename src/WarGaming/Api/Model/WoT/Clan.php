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

use WarGaming\Api\Annotation\Id;
use WarGaming\Api\Model\Collection;
use WarGaming\Api\Util\DateTime;

/**
 * Clan model
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Clan
{
    /**
     * @var integer
     *
     * @Id
     */
    public $id;

    /**
     * @var string
     */
    public $abbreviation;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $isClanDisbanded;

    /**
     * @var string
     */
    public $motto;

    /**
     * @var string
     */
    public $color;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var \DateTime
     */
    public $updatedAt;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $descriptionHtml;

    /**
     * @var AccountCollection|Account[]
     */
    public $members = array();

    /**
     * @var Collection|Province[]
     */
    public $provinces = array();

    /**
     * Construct
     */
    public function __construct()
    {
        $this->members = new Collection();
        $this->provinces = new Collection();
    }

    /**
     * Get tanks
     *
     * @return Collection
     */
    public function getTanks()
    {
        $tanks = new Collection();

        foreach ($this->members as $member) {
            foreach ($member->tanks as $accountTank) {
                $tanks[] = $accountTank->tank;
            }
        }

        return $tanks;
    }

    /**
     * Create new instance from array
     *
     * @param array $data
     *
     * @return Clan
     */
    public static function createFromArray(array $data)
    {
        /** @var Clan $clan */
        $clan = new static();

        $clan->id = isset($data['clan_id']) ? $data['clan_id'] : null;

        $clan->provinces = new Collection();
        if (!empty($data['provinces'])) {
            foreach ($data['provinces'] as $provinceInfo) {
                $clan->provinces[] = Province::createFromArray($provinceInfo);
            }
        }

        return $clan;
    }

    /**
     * Set full data to instance with array
     *
     * @param array $data
     */
    public function setFullDataFromArray(array $data)
    {
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->abbreviation = isset($data['abbreviation']) ? $data['abbreviation'] : null;
        $this->isClanDisbanded = isset($data['is_clan_disbanded']) ? (bool) $data['is_clan_disbanded'] : null;
        $this->motto = isset($data['motto']) ? $data['motto'] : null;
        $this->color = isset($data['color']) ? $data['color'] : null;
        $this->description = isset($data['description']) ? $data['description'] : null;
        $this->descriptionHtml = isset($data['description_html']) ? $data['description_html'] : null;

        // Datetime objects
        $this->createdAt = isset($data['created_at']) ? DateTime::dateTimeFromTimestamp($data['created_at']) : null;
        $this->updatedAt = isset($data['updated_at']) ? DateTime::dateTimeFromTimestamp($data['updated_at']) : null;

        // Grouping members
        $this->members = new AccountCollection();
        if (isset($data['members'])) {
            foreach ($data['members'] as $memberInfo) {
                $this->members[] = Account::createFromArray($memberInfo);
            }
        }
    }
}

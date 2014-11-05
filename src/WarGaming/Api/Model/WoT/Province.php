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
 * Province model
 *
 * @author Mike Flisher <mike.flisher@gmail.com>
 */
class Province
{
    /**
     * @var string
     * 
     * @Id
     */
    public $id;

    /**
     * @var string
     */
    // public $arena; // this field will be disabled by WarGaming

    /**
     * @var string
     */
    public $arenaI18n;

    /**
     * @var integer
     *
     */
    public $arenaId;

    /**
     * @var integer
     *
     */
    public $clanId;

    /**
     * @var NeighborsCollection|Neighbor[]
     */
    public $neighbors = array();

    /**
     * @var string
     */
    public $primaryRegion;

    /**
     * @var integer
     *
     */
    public $primeTime;

    /**
     * @var string
     */
    public $provinceI18n;

    /**
     * @var string
     */
    public $provinceId;

    /**
     * @var integer
     *
     */
    public $revenue;

    /**
     * @var string
     */
    public $status;

    /**
     * @var \DateTime
     */
    public $updatedAt;

    /**
     * @var integer
     *
     */
    public $vehicleMaxLevel;

    /**
     * @var RegionssCollection|Region[]
     */
    public $regions = array();

    /**
     * Get neighbors
     *
     * @return NeighborsCollection
     */
    public function getNeighbors()
    {
        return $this->neighbors->getNeighbors();
        // return $this->neighbors;
    }

    /**
     * Get regions
     *
     * @return RegionsCollection
     */
    public function getRegions()
    {
        return $this->regions->getRegions();
        // return $this->regions;
    }

    /**
     * Create new province from array
     *
     * @param array $data
     *
     * @return Province
     */
    public static function createFromArray(array $data)
    {
        /** @var Province $province */
        $province = new static();

        $province->id = isset($data['province_id']) ? $data['province_id'] : null;

        $province->neighbors = new Collection();
        if (!empty($data['neighbors'])) {
            foreach ($data['neighbors'] as $neighborInfo) {
                $province->neighbors[$neighborInfo] = Neighbor::createFromArray(array("neighbor_id" => $neighborInfo));
            }
        }

        $province->regions = new Collection();
        if (!empty($data['regions'])) {
            foreach ($data['regions'] as $regionInfo) {
                $province->regions[$regionInfo['region_id']] = Region::createFromArray($regionInfo);
            }
        }

        return $province;
    }

    /**
     * Set full data to instance with array
     *
     * @param array $data
     */
    public function setFullDataFromArray(array $data)
    {

        $this->id              = isset($data['province_id']) ? $data['province_id'] : null;
        // $this->arena           = isset($data['arena']) ? $data['arena'] : null; // this field will be disabled by WarGaming
        $this->arenaI18n       = isset($data['arena_i18n']) ? $data['arena_i18n'] : null;
        $this->arenaId         = isset($data['arena_id']) ? $data['arena_id'] : null;
        $this->clanId          = isset($data['clan_id']) ? $data['clan_id'] : null;
        $this->primaryRegion   = isset($data['primary_region']) ? $data['primary_region'] : null;
        $this->primeTime       = isset($data['prime_time']) ? DateTime::dateTimeFromPrimeTime($data['prime_time']) : null;
        // $this->primeTime       = isset($data['prime_time']) ? $data['prime_time'] : null; // api returns prime_time as an hour in 24h format
        $this->provinceI18n    = isset($data['province_i18n']) ? $data['province_i18n'] : null;
        $this->provinceId      = isset($data['province_id']) ? $data['province_id'] : null;
        $this->revenue         = isset($data['revenue']) ? $data['revenue'] : null;
        $this->status          = isset($data['status']) ? $data['status'] : null;
        $this->updatedAt       = isset($data['updated_at']) ? DateTime::dateTimeFromTimestamp($data['updated_at']) : null;
        $this->vehicleMaxLevel = isset($data['vehicle_max_level']) ? $data['vehicle_max_level'] : null;

        $this->neighbors = new NeighborCollection();
        if (isset($data['neighbors'])) {
            foreach ($data['neighbors'] as $neighborInfo) {
                $this->neighbors[$neighborInfo] = Neighbor::createFromArray(array("neighbor_id" => $neighborInfo));
            }
        }

        $this->regions = new RegionCollection();
        if (isset($data['regions'])) {
            foreach ($data['regions'] as $regionInfo) {
                $this->regions[$regionInfo['region_id']] = Region::createFromArray($regionInfo);
            }
        }
    }
}

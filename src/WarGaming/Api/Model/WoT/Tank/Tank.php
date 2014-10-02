<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Model\WoT\Tank;

use WarGaming\Api\Annotation\Id;
use WarGaming\Api\Model\Collection;

/**
 * Tank model
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Tank
{
    /**
     * @var int
     *
     * @Id
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $nation;

    /**
     * @var int
     */
    public $priceCredit;

    /**
     * @var int
     */
    public $priceGold;

    /**
     * @var bool
     */
    public $isGift;

    /**
     * @var bool
     */
    public $isPremium;

    /**
     * @var int
     */
    public $level;

    /**
     * @var int
     */
    public $limitWeight;

    /**
     * @var int
     */
    public $weight;

    /**
     * @var int
     */
    public $maxHealth;

    /**
     * @var int
     */
    public $enginePower;

    /**
     * @var float
     */
    public $speedLimit;

    /**
     * @var int
     */
    public $chassisRotationSpeed;

    /**
     * @var int
     */
    public $circularVisionRadius;

    /**
     * @var int
     */
    public $gunDamageMin;

    /**
     * @var int
     */
    public $gunDamageMax;

    /**
     * @var int
     */
    public $gunMaxAmmo;

    /**
     * @var int
     */
    public $gunName;

    /**
     * @var int
     */
    public $gunPiercingPowerMax;

    /**
     * @var int
     */
    public $gunPiercingPowerMin;

    /**
     * @var float
     */
    public $gunRate;

    /**
     * @var int
     */
    public $radioDistance;

    /**
     * @var int
     */
    public $turretArmorBoard;

    /**
     * @var int
     */
    public $turretArmorFedd;

    /**
     * @var int
     */
    public $turretArmorForehead;

    /**
     * @var int
     */
    public $turretRotationSpeed;

    /**
     * @var int
     */
    public $vehicleArmorBoard;

    /**
     * @var int
     */
    public $vehicleArmorFedd;

    /**
     * @var int
     */
    public $vehicleArmorForehead;

    /**
     * @var Collection|TankCrew[]
     */
    public $crews;

    /**
     * @var Collection|TankEngine[]
     */
    public $engines;

    /**
     * @var Collection|TankGun[]
     */
    public $guns;

    /**
     * @var Collection|TankRadio[]
     */
    public $radios;

    /**
     * @var Collection|TankTurret[]
     */
    public $turrets;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->crews = new Collection();
        $this->engines = new Collection();
        $this->guns = new Collection();
        $this->radios = new Collection();
        $this->turrets = new Collection();
    }

    /**
     * Create new instance from data
     *
     * @param array $data
     *
     * @return Tank
     */
    public static function createFromArray(array $data)
    {
        /** @var Tank $tank */
        $tank = new static();

        $tank->id = isset($data['id']) ? $data['id'] : null;


        return $tank;
    }

    /**
     * Set full data from array
     *
     * @param array $data
     */
    public function setFullDataFromArray(array $data)
    {
        $this->type = isset($data['type']) ? $data['type'] : null;
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->nation = isset($data['nation']) ? $data['nation'] : null;

        $this->priceCredit = isset($data['price_credit']) ? $data['price_credit'] : null;
        $this->priceGold = isset($data['price_gold']) ? $data['price_gold'] : null;

        $this->isGift = isset($data['is_gift']) ? $data['is_gift'] : false;
        $this->isPremium = isset($data['is_premium']) ? $data['is_premium'] : false;

        $this->level = isset($data['level']) ? $data['level'] : null;
        $this->limitWeight = isset($data['limit_weight']) ? $data['limit_weight'] : null;
        $this->weight = isset($data['weight']) ? $data['weight'] : null;
        $this->maxHealth = isset($data['max_health']) ? $data['max_health'] : null;

        $this->enginePower = isset($data['engine_power']) ? $data['engine_power'] : null;
        $this->speedLimit = isset($data['speed_limit']) ? $data['speed_limit'] : null;

        $this->chassisRotationSpeed = isset($data['chassis_rotation_speed']) ? $data['chassis_rotation_speed'] : null;
        $this->circularVisionRadius = isset($data['circular_vision_radius']) ? $data['circular_vision_radius'] : null;

        $this->radioDistance = isset($data['radio_distance']) ? $data['radio_distance'] : null;

        $this->gunDamageMin = isset($data['gun_damage_min']) ? $data['gun_damage_min'] : null;
        $this->gunDamageMax = isset($data['gun_damage_max']) ? $data['gun_damage_max'] : null;
        $this->gunMaxAmmo = isset($data['gun_max_ammo']) ? $data['gun_max_ammo'] : null;
        $this->gunName = isset($data['gun_name']) ? $data['gun_name'] : null;
        $this->gunPiercingPowerMax = isset($data['gun_piercing_power_max']) ? $data['gun_piercing_power_max'] : null;
        $this->gunPiercingPowerMin = isset($data['gun_piercing_power_min']) ? $data['gun_piercing_power_min'] : null;
        $this->gunRate = isset($data['gun_rate']) ? $data['gun_rate'] : null;

        $this->turretArmorBoard = isset($data['turret_armor_board']) ? $data['turret_armor_board'] : null;
        $this->turretArmorFedd = isset($data['turret_armor_fedd']) ? $data['turret_armor_fedd'] : null;
        $this->turretArmorForehead = isset($data['turret_armor_forehead']) ? $data['turret_armor_forehead'] : null;
        $this->turretRotationSpeed = isset($data['turret_rotation_speed']) ? $data['turret_rotation_speed'] : null;

        $this->vehicleArmorBoard = isset($data['vehicle_armor_board']) ? $data['vehicle_armor_board'] : null;
        $this->vehicleArmorFedd = isset($data['vehicle_armor_fedd']) ? $data['vehicle_armor_fedd'] : null;
        $this->vehicleArmorForehead = isset($data['vehicle_armor_forehead']) ? $data['vehicle_armor_forehead'] : null;

        $this->crews = new Collection();
        if (!empty($data['crew'])) {
            foreach ($data['crew'] as $crewInfo) {
                $this->crews[] = TankCrew::createFromArray($crewInfo);
            }
        }

        $this->engines = new Collection();
        if (!empty($data['engines'])) {
            foreach ($data['engines'] as $engineInfo) {
                $this->engines[] = TankEngine::createFromArray($engineInfo);
            }
        }

        $this->guns = new Collection();
        if (!empty($data['guns'])) {
            foreach ($data['guns'] as $gunInfo) {
                $this->guns[] = TankGun::createFromArray($gunInfo);
            }
        }

        $this->radios = new Collection();
        if (!empty($data['radios'])) {
            foreach ($data['radios'] as $radioInfo) {
                $this->radios[] = TankRadio::createFromArray($radioInfo);
            }
        }

        $this->turrets = new Collection();
        if (!empty($data['turrets'])) {
            foreach ($data['turrets'] as $turretInfo) {
                $this->turrets[] = TankTurret::createFromArray($turretInfo);
            }
        }
    }
}

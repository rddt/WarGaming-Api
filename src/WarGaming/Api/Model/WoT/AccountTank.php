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

use WarGaming\Api\Model\WoT\Tank\Tank;

/**
 * Account tank model
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class AccountTank
{
    /**
     * @var Account
     */
    public $account;

    /**
     * @var Tank
     */
    public $tank;

    /**
     * @var int
     */
    public $markOfMastery;

    /**
     * @var int
     */
    public $battles;

    /**
     * @var int
     */
    public $wins;

    /**
     * Create account tank instance form array
     *
     * @param array $data
     *
     * @return AccountTank
     */
    public static function createFromArray(array $data)
    {
        /** @var AccountTank $accountTank */
        $accountTank = new static();

        $accountTank->markOfMastery = isset($data['mark_of_mastery']) ? $data['mark_of_mastery'] : null;
        $accountTank->battles = isset($data['statistics']['battles']) ? $data['statistics']['battles'] : null;
        $accountTank->wins = isset($data['statistics']['wins']) ? $data['statistics']['wins'] : null;

        if (isset($data['tank_id'])) {
            $accountTank->tank = new Tank();
            $accountTank->tank->id = $data['tank_id'];
        }

        return $accountTank;
    }
}

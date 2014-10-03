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

use WarGaming\Api\Model\Collection;

/**
 * Clan collection
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class AccountCollection extends Collection
{
    /**
     * @var array|Account[]
     */
    protected $storage;

    /**
     * Get tanks
     *
     * @return Collection|\WarGaming\Api\Model\WoT\Tank\Tank
     */
    public function getTanks()
    {
        $collection = new Collection();

        foreach ($this->storage as $account) {
            foreach ($account->tanks as $accountTank) {
                $collection[] = $accountTank->tank;
            }
        }

        return $collection;
    }
}
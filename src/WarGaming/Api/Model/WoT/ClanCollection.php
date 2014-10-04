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
class ClanCollection extends Collection
{
    /**
     * @var array|Clan[]
     */
    protected $storage;

    /**
     * Get accounts
     *
     * @return AccountCollection|Account[]
     */
    public function getAccounts()
    {
        $accounts = new AccountCollection();

        foreach ($this->storage as $clan) {
            $accounts->addCollection($clan->members);
        }

        return $accounts;
    }
}

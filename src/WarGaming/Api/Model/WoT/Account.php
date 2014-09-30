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
use WarGaming\Api\Util\DateTime;

/**
 * Account model
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Account
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
    public $name;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var string
     */
    public $role;

    /**
     * Create instance from array
     *
     * @param array $data
     *
     * @return Account
     */
    public static function createFromArray(array $data)
    {
        /** @var Account $account */
        $account = new static();

        $account->id = isset($data['account_id']) ? $data['account_id'] : null;
        $account->name = isset($data['account_name']) ? $data['account_name'] : null;
        $account->role = isset($data['role']) ? $data['role'] : null;
        $account->createdAt = isset($data['created_at']) ? DateTime::dateTimeFromTimestamp($data['created_at']) : null;

        return $account;
    }
}
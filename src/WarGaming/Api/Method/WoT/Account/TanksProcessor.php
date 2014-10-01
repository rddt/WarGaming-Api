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

use Guzzle\Http\Message\Response as GuzzleResponse;
use WarGaming\Api\Method\AbstractProcessor;
use WarGaming\Api\Method\MethodInterface;
use WarGaming\Api\Model\WoT\AccountTank;

/**
 * Account tank processor
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TanksProcessor extends AbstractProcessor
{
    /**
     * {@inheritDoc}
     */
    protected function getApiUri()
    {
        return 'wot/account/tanks';
    }

    /**
     * {@inheritDoc}
     */
    public function parseResponse(array $data, array $fullData, GuzzleResponse $response, MethodInterface $method)
    {
        /** @var Tanks $method */
        foreach ($method->accounts as $account) {
            if (!empty($data[$account->id])) {
                foreach ($data[$account->id] as $accountTankInfo) {
                    $accountTank = AccountTank::createFromArray($accountTankInfo);
                    $account->tanks[] = $accountTank;
                }
            }
        }
    }
}

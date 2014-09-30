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

use Guzzle\Http\Message\Response as GuzzleResponse;
use WarGaming\Api\Method\AbstractProcessor;
use WarGaming\Api\Method\MethodInterface;

/**
 * Clan info API processor
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ClanInfoProcessor extends AbstractProcessor
{
    /**
     * {@inheritDoc}
     */
    protected function getApiUri()
    {
        return 'wot/clan/info';
    }

    /**
     * {@inheritDoc}
     */
    public function parseResponse(array $data, array $fullData, GuzzleResponse $response, MethodInterface $method)
    {
        /** @var ClanInfo $method */
        $processClans = $method->clans;

        foreach ($data as $clanInfo) {
            if (!isset($clanInfo['clan_id'])) {
                continue;
            }

            $clanInfoId = $clanInfo['clan_id'];

            // Search clan in storage
            foreach ($processClans as $index => $clan) {
                if ($clan->id == $clanInfoId) {
                    $clan->setFullDataFromArray($clanInfo);
                    unset ($processClans[$index]);
                }
            }
        }

        return $method->clans;
    }
}

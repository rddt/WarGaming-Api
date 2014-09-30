<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Method\WoT\GlobalWar;

use Guzzle\Http\Message\Response as GuzzleResponse;
use WarGaming\Api\Method\AbstractProcessor;
use WarGaming\Api\Method\MethodInterface;
use WarGaming\Api\Model\WoT\Clan;

/**
 * Get clans API processor
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ClansProcessor extends AbstractProcessor
{
    /**
     * {@inheritDoc}
     */
    protected function getApiUri()
    {
        return 'wot/globalwar/clans';
    }

    /**
     * {@inheritDoc}
     */
    public function parseResponse(array $data, array $fullData, GuzzleResponse $response, MethodInterface $method)
    {
        $clans = array();

        foreach ($data as $info) {
            $clans[] = Clan::createFromArray($info);
        }

        return $clans;
    }
}
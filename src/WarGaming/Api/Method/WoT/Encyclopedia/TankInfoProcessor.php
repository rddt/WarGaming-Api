<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Method\WoT\Encyclopedia;

use Guzzle\Http\Message\Response as GuzzleResponse;
use WarGaming\Api\Method\AbstractProcessor;
use WarGaming\Api\Method\MethodInterface;

/**
 * Tank info API processor
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TankInfoProcessor extends AbstractProcessor
{
    /**
     * Get API Uri
     *
     * @return string
     */
    protected function getApiUri()
    {
        return 'wot/encyclopedia/tankinfo';
    }

    /**
     * {@inheritDoc}
     */
    public function parseResponse(array $data, array $fullData, GuzzleResponse $response, MethodInterface $method)
    {
        /** @var TankInfo $method */
        foreach ($method->tanks as $index => $tank) {
            if (!empty($data[$tank->id])) {
                $tank->setFullDataFromArray($data[$tank->id]);
            } else {
                unset ($method->tanks[$index]);
            }
        }
    }
}

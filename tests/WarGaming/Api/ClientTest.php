<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api;

/**
 * Client testing
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test set api mode, set region and set host
     *
     * @dataProvider providerSetApiModeAndSetRegion
     *
     * @param string $apiMode
     * @param string $region
     * @param bool   $host
     * @param string $expected
     * @param bool   $apiModeIsInvalid
     * @param bool   $regionIsInvalid
     */
    public function testSetApiModeAndSetRegion($apiMode, $region, $host, $expected, $apiModeIsInvalid, $regionIsInvalid)
    {
        if ($regionIsInvalid) {
            $this->setExpectedException('InvalidArgumentException');
        }

        if ($apiModeIsInvalid) {
            $this->setExpectedException('InvalidArgumentException');
        }

        // Create client mock
        /** @var Client $client */
        $client = $this->getMock(
            'WarGaming\Api\Client',
            null,
            array(),
            '',
            false
        );

        if (null !== $apiMode) {
            $client->setApiMode($apiMode);
        }

        if (null !== $host) {
            $client->setHost($host);
        }

        if (null !== $region) {
            $client->setRegion($region);
        }

        $requestHost = $client->getRequestHost();

        $this->assertEquals($expected, $requestHost);
    }

    /**
     * Data provider for testing set api mode, set region and set host
     *
     * @return array
     */
    public function providerSetApiModeAndSetRegion()
    {
        return array(
            // Correct data
            array(Client::TANKS, Client::REGION_KOREA, null, 'api.worldoftanks.kr', false, false),
            array(Client::TANKS, Client::REGION_NORTH_AMERICA, null, 'api.worldoftanks.com', false, false),
            array(Client::TANKS, Client::REGION_RUSSIA, null, 'api.worldoftanks.ru', false, false),
            array(Client::TANKS, Client::REGION_EUROPE, null, 'api.worldoftanks.eu', false, false),
            array(Client::TANKS, Client::REGION_ASIA, null, 'api.worldoftanks.asia', false, false),

            array(Client::PLANES, Client::REGION_KOREA, null, 'api.worldofwarplanes.kr', false, false),
            array(Client::PLANES, Client::REGION_NORTH_AMERICA, null, 'api.worldofwarplanes.com', false, false),
            array(Client::PLANES, Client::REGION_RUSSIA, null, 'api.worldofwarplanes.ru', false, false),
            array(Client::PLANES, Client::REGION_EUROPE, null, 'api.worldofwarplanes.eu', false, false),
            array(Client::PLANES, Client::REGION_ASIA, null, 'api.worldofwarplanes.asia', false, false),

            // Valid apimode and setting a custom host
            array(Client::TANKS, null, 'example.com', 'example.com', false, false),
            array(Client::PLANES, null, 'example.ru', 'example.ru', false, false),

            // Invalid data
            // Invalid apimode and invalid region
            array('invalid_api_mode', 'invalid_region', null, 'api.worldoftanks.ru', true, true),
            // Invalid apimode and no region
            array('invalid_api_mode', null, null, 'api.worldoftanks.ru', true, false),
            // Invalid apimode with valid region
            array('invalid_api_mode', Client::REGION_RUSSIA, null, 'api.worldoftanks.ru', true, false),
            // Invalid apimode, no region and setting a custom host
            array('invalid_api_mode', null, 'api.worldoftanks.ru', 'api.worldoftanks.ru', true, false),
            // Invalid region and no custom host for all apimodes
            array(Client::TANKS, 'invalid_region', null, 'api.worldoftanks.ru', false, true),
            array(Client::PLANES, 'invalid_region', null, 'api.worldoftanks.ru', false, true)
        );
    }
}

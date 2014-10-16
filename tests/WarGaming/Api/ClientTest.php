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
     * Test change request host and region
     *
     * @dataProvider providerChangeRequestHostAndRegion
     *
     * @param string $baseHost
     * @param string $region
     * @param bool   $customHost
     * @param string $expected
     * @param bool   $regionIsInvalid
     */
    public function testChangeRequestHostAndRegion($baseHost, $region, $customHost, $expected, $regionIsInvalid)
    {
        if ($regionIsInvalid) {
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

        if (null !== $baseHost) {
            $client->setHost($baseHost);
        }

        if (null !== $region) {
            $client->setRegion($region);
        }

        $client->setCustomHost($customHost);

        $requestHost = $client->getRequestHost();

        $this->assertEquals($expected, $requestHost);
    }

    /**
     * Data provider for testing change request host and region
     *
     * @return array
     */
    public function providerChangeRequestHostAndRegion()
    {
        return array(
            // Correct data
            array(null, null, false, 'api.worldoftanks.ru', false),
            array('foo-tanks', null, false, 'foo-tanks.ru', false),
            array('foo-tanks', 'asia', false, 'foo-tanks.asia', false),
            array('foo.bar', null, false, 'foo.bar.ru', false),
            array('foo.bar', null, true, 'foo.bar', false),

            array('foo.bar', 'kr', false, 'foo.bar.kr', false),
            array('foo.bar', 'com', false, 'foo.bar.com', false),
            array('foo.bar', 'eu', false, 'foo.bar.eu', false),
            array('foo.bar', 'ru', false, 'foo.bar.ru', false),
            array('foo.bar', 'AsIa', false, 'foo.bar.asia', false),

            // Invalid data
            array('foo.bar', 'foo', true, null, true)
        );
    }
}

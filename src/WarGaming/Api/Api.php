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

use WarGaming\Api\Method\MethodInterface;
use WarGaming\Api\Method\PagerMethodInterface;
use WarGaming\Api\Method\WoT\Clan\ClanInfo;
use WarGaming\Api\Method\WoT\GlobalWar\Clans;

/**
 * WarGaming Api wrapper
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Api
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ClientCollectionLoader
     */
    private $clientCollectionLoader;

    /**
     * @var ClientFullLoader
     */
    private $clientFullLoader;

    /**
     * Construct
     *
     * @param Client                 $client
     * @param ClientCollectionLoader $collectionLoader
     * @param ClientFullLoader       $fullLoader
     */
    public function __construct(Client $client, ClientCollectionLoader $collectionLoader, ClientFullLoader $fullLoader)
    {
        $this->client = $client;
        $this->clientCollectionLoader = $collectionLoader;
        $this->clientFullLoader = $fullLoader;
    }

    /**
     * Request method
     *
     * @param MethodInterface $method
     *
     * @return mixed
     */
    public function request(MethodInterface $method)
    {
        return $this->client->request($method);
    }

    /**
     * Request method with collection loader
     *
     * @param MethodInterface $method
     *
     * @return array
     */
    public function requestCollection(MethodInterface $method)
    {
        return $this->clientCollectionLoader->request($method);
    }

    /**
     * Request method with full loader (With pagination)
     * Load all data from all pages.
     *
     * @param PagerMethodInterface $method
     *
     * @return array
     */
    public function requestFull(PagerMethodInterface $method)
    {
        return $this->clientFullLoader->request($method);
    }

    /**
     * Load clans for map
     *
     * @param string $map
     *
     * @return array|\WarGaming\Api\Model\WoT\Clan[]
     */
    public function loadClans($map = 'globalmap')
    {
        $method = new Clans();
        $method->map = $map;

        return $this->clientFullLoader->request($method);
    }

    /**
     * Load clans info
     * Attention: not returning! All data saves in each clan instance.
     *
     * @param array|\WarGaming\Api\Model\WoT\Clan[] $clans
     */
    public function loadClansInfo(array $clans)
    {
        $method = new ClanInfo();
        $method->clans = $clans;

        $this->clientCollectionLoader->request($method);
    }
}

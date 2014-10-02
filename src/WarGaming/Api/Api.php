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
use WarGaming\Api\Method\WoT\Account\Tanks;
use WarGaming\Api\Method\WoT\Clan\ClanInfo;
use WarGaming\Api\Method\WoT\Encyclopedia\TankInfo;
use WarGaming\Api\Method\WoT\GlobalWar\Clans;
use WarGaming\Api\Method\WoT\GlobalWar\Maps;

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
     * @var ClientCollectionCacheLoader
     */
    private $clientCollectionCacheLoader;

    /**
     * @var ClientFullLoader
     */
    private $clientFullLoader;

    /**
     * Construct
     *
     * @param Client                      $client
     * @param ClientCollectionLoader      $collectionLoader
     * @param ClientCollectionCacheLoader $collectionCacheLoader
     * @param ClientFullLoader            $fullLoader
     */
    public function __construct(
        Client $client,
        ClientCollectionLoader $collectionLoader,
        ClientCollectionCacheLoader $collectionCacheLoader,
        ClientFullLoader $fullLoader
    ) {
        $this->client = $client;
        $this->clientCollectionLoader = $collectionLoader;
        $this->clientCollectionCacheLoader = $collectionCacheLoader;
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
     * Request method with collection cache loader
     *
     * @param MethodInterface $method
     *
     * @return mixed
     */
    public function requestCollectionCache(MethodInterface $method)
    {
        return $this->clientCollectionCacheLoader->request($method);
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
     * Load maps
     *
     * @return array|\WarGaming\Api\Model\WoT\GlobalMap
     */
    public function loadGlobalMaps()
    {
        $method = new Maps();

        return $this->client->request($method);
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

    /**
     * Load account tanks
     * Attention: not returning! All data saves in each account instance.
     *
     * @param array|\WarGaming\Api\Model\WoT\Account[] $accounts
     */
    public function loadAccountTanks(array $accounts)
    {
        $method = new Tanks();
        $method->accounts = $accounts;

        $this->clientCollectionLoader->request($method);
    }

    /**
     * Load tanks info
     * Attention: not returning! All data saves in each tank instance.
     *
     * @param array|\WarGaming\Api\Model\WoT\Tank\Tank[] $tanks
     */
    public function loadTanksInfo(array $tanks)
    {
        $method = new TankInfo();
        $method->tanks = $tanks;

        $this->clientCollectionCacheLoader->request($method);
    }
}

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

use WarGaming\Api\Method\PagerMethodInterface;

/**
 * Load data from all pages.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ClientFullLoader
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var integer
     */
    private $defaultLimit = 100;

    /**
     * Construct
     *
     * @param Client  $client
     * @param integer $defaultLimit
     */
    public function __construct(Client $client, $defaultLimit = 100)
    {
        $this->client = $client;
        $this->setDefaultLimit($defaultLimit);
    }

    /**
     * Set default limit
     *
     * @param int $limit
     *
     * @return ClientFullLoader
     *
     * @throws \InvalidArgumentException
     */
    public function setDefaultLimit($limit)
    {
        if (!is_int($limit)) {
            throw new \InvalidArgumentException(sprintf(
                'The limit must be a integer value, "%s" given.',
                $limit
            ));
        }

        if ($limit < 1 || $limit > 100) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid limit "%d". Must be a more then 0 and less then 100',
                $limit
            ));
        }

        $this->defaultLimit = $limit;
    }

    /**
     * Load all data
     *
     * @param PagerMethodInterface $method
     * @param integer              $limit
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    public function request(PagerMethodInterface $method, $limit = null)
    {
        if (!$limit) {
            $limit = $this->defaultLimit;
        }

        // Start iteration
        $activePage = 1;
        $method->setLimit($limit);

        // Attention: Start infinite loop!
        $result = array();

        while (true) {
            $method->setPage($activePage);
            $data = $this->client->request($method);

            if (is_array($data)) {
                $result = array_merge($result, $data);
            } else {
                throw new \RuntimeException(sprintf(
                    'The processor for method "%s" must be return array, but "%s" given.',
                    get_class($method),
                    is_object($data) ? get_class($data) : gettype($data)
                ));
            }

            if (!count($data) || count($data) < $limit) {
                // Exit from loop. This is a last page.
                break;
            }

            $activePage++;
        }

        return $result;
    }
}

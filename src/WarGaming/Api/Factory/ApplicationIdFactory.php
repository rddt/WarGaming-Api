<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Factory;

/**
 * Application ID Factory
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ApplicationIdFactory implements ApplicationIdFactoryInterface
{
    /**
     * @var array
     */
    private $applicationIds = array();

    /**
     * Used latest request
     *
     * @var array
     */
    private $useApplicationIds = array();

    /**
     * Construct
     *
     * @param array $applicationIds
     */
    public function __construct(array $applicationIds)
    {
        foreach ($applicationIds as $applicationId) {
            $this->addApplicationId($applicationId);
        }
    }

    /**
     * Add application id
     *
     * @param string $applicationId
     * @param int    $requestsPerSecond
     *
     * @return ApplicationIdFactory
     */
    public function addApplicationId($applicationId, $requestsPerSecond = 2)
    {
        $this->applicationIds[$applicationId] = array(
            'application_id' => $applicationId,
            'requests_per_second' => $requestsPerSecond
        );

        $this->useApplicationIds[$applicationId] = array();

        return $this;
    }

    /**
     * Remove application id
     *
     * @param string $applicationId
     *
     * @return ApplicationIdFactory
     */
    public function removeApplicationId($applicationId)
    {
        unset ($this->applicationIds[$applicationId]);
        unset ($this->useApplicationIds[$applicationId]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getApplicationId()
    {
        if (!count($this->applicationIds)) {
            throw new \RuntimeException('Not found available application ids in factory. Please add application id.');
        }

        // Attention: start infinity loop!
        while (true) {
            $this->removeOldestUses();

            foreach ($this->applicationIds as $applicationId => $info) {
                if ($this->isCanUseApplicationId($applicationId)) {
                    return $applicationId;
                }
            }

            usleep(5000);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function process($applicationId)
    {
        $this->useApplicationIds[$applicationId][] = microtime(true);
    }

    /**
     * Is can use application id
     *
     * @param string $applicationId
     *
     * @return bool
     */
    private function isCanUseApplicationId($applicationId)
    {
        if (!count($this->useApplicationIds[$applicationId])) {
            return true;
        }

        $minUTime = min($this->useApplicationIds[$applicationId]);

        if ($minUTime > microtime(true) - 1) {
            return count($this->useApplicationIds[$applicationId]) < $this->applicationIds[$applicationId]['requests_per_second'];
        }

        // Must be remove oldest hashes
        return false;
    }

    /**
     * Remove oldest
     */
    private function removeOldestUses()
    {
        $uTime = microtime(true) - 1;

        foreach ($this->useApplicationIds as $applicationId => $usesApplicationId) {
            foreach ($usesApplicationId as $index => $startUTime) {
                if ($startUTime < $uTime) {
                    unset ($this->useApplicationIds[$applicationId][$index]);
                }
            }
        }
    }
}

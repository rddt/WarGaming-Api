<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Method;

/**
 * Pager method
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface PagerMethodInterface extends MethodInterface
{
    /**
     * Set limit
     *
     * @param int $limit
     */
    public function setLimit($limit);

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit();

    /**
     * Set page
     *
     * @param int $page
     */
    public function setPage($page);

    /**
     * Get page
     *
     * @return int
     */
    public function getPage();
}

<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Util;

/**
 * Date time
 */
class DateTime
{
    /**
     * Create date time from timestamp
     *
     * @param int $timestamp
     *
     * @return \DateTime
     */
    public static function dateTimeFromTimestamp($timestamp)
    {
        $datetime = new \DateTime();
        $datetime->setTimestamp($timestamp);

        return $datetime;
    }
}
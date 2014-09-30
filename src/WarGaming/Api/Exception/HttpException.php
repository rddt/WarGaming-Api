<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Exception;

/**
 * Http error exception
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class HttpException extends Exception
{
    /**
     * Construct
     *
     * @param int        $code
     * @param string     $message
     * @param \Exception $previous
     */
    public function __construct($code, $message = null, \Exception $previous = null)
    {
        if (!$message) {
            $message = sprintf('Http error with code "%d".', $code);
        }

        return parent::__construct($message, $code, $previous);
    }
}
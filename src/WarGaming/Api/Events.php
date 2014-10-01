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
 * Event list
 */
final class Events
{
    const REQUEST_START             = 'wargaming_api.request_start';
    const REQUEST_COMPLETE          = 'wargaming_api.request_complete';
    const REQUEST_ERROR             = 'wargaming_api.request_error';

    /**
     * Disable constructor
     */
    private function __construct()
    {
    }
}

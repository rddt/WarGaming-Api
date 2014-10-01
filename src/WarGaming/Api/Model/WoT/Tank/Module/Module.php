<?php

/**
 * This file is part of the WarGaming API package
 *
 * (c) Vitaliy Zhuk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace WarGaming\Api\Model\WoT\Tank\Module;

use WarGaming\Api\Annotation\Id;

/**
 * Abstract layer for module
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
abstract class Module
{
    /**
     * @var int
     *
     * @Id
     */
    public $id;
}

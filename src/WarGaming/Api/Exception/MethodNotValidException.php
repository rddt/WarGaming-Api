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

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Method not valid error
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class MethodNotValidException extends Exception
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $violationList;

    /**
     * Construct
     *
     * @param ConstraintViolationListInterface $violationList
     */
    public function __construct(ConstraintViolationListInterface $violationList)
    {
        $this->violationList = $violationList;
    }

    /**
     * Get violation list
     *
     * @return ConstraintViolationListInterface
     */
    public function getViolationList()
    {
        return $this->violationList;
    }
}
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
 * Exception factory
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ExceptionFactory
{
    /**
     * Http error
     *
     * @param int        $code
     * @param string     $message
     * @param \Exception $previous
     *
     * @return HttpException
     */
    public static function httpError($code, $message = null, \Exception $previous = null)
    {
        return new HttpException($code, $message, $previous);
    }

    /**
     * Http error: Access denied
     *
     * @param \Exception $previous
     *
     * @return HttpException
     */
    public static function httpAccessDenied(\Exception $previous = null)
    {
        return new HttpException(403, "Access denied", $previous);
    }

    /**
     * Http error: Page not found 404
     *
     * @param \Exception $previous
     *
     * @return HttpException
     */
    public static function httpPageNotFound(\Exception $previous = null)
    {
        return new HttpException(404, "Page not found", $previous);
    }

    /**
     * Http error: Server error 500
     *
     * @param \Exception
     *
     * @return HttpException
     */
    public static function httpServerError(\Exception $previous = null)
    {
        return new HttpException(500, "Server error", $previous);
    }

    /**
     * Missing application id
     *
     * @return MissingApplicationIdException
     */
    public static function missingApplicationId()
    {
        return new MissingApplicationIdException('Missing application ID.');
    }

    /**
     * Unavailable status
     *
     * @param string $status
     *
     * @return UnavailableStatusException
     */
    public static function unavailableStatus($status)
    {
        return new UnavailableStatusException(sprintf(
            'Unavailable status "%s".',
            $status
        ));
    }

    /**
     * Method not valid
     *
     * @param ConstraintViolationListInterface $violationList
     *
     * @return MethodNotValidException
     */
    public static function methodNotValid(ConstraintViolationListInterface $violationList)
    {
        return new MethodNotValidException($violationList);
    }

    /**
     * Missing key
     *
     * @param string $key
     *
     * @return MissingKeyException
     */
    public static function missingKeyInResponse($key)
    {
        $message = sprintf('Missing key "%s" in response.', $key);

        return new MissingKeyException($message);
    }

    /**
     * Request error from WarGaming response
     *
     * @param array $errorInfo
     *
     * @return RequestErrorException
     */
    public static function requestErrorFromWarGamingResponse(array $errorInfo)
    {
        return RequestErrorException::createFromWarGamingResponse($errorInfo);
    }
}
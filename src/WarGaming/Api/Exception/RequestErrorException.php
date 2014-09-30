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
 * Request error exception
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class RequestErrorException extends Exception
{
    const UNDEFINED                     = 0;

    /** Field errors */
    const FIELD_NOT_SPECIFIED           = 1;
    const FIELD_NOT_FOUND               = 2;
    const FIELD_LIST_LIMIT_EXCEEDED     = 3;
    const FIELD_INVALID                 = 4;

    /** Method errors */
    const METHOD_NOT_FOUND              = 21;
    const METHOD_DISABLED               = 22;

    /** Application errors */
    const APPLICATION_IS_BLOCKED        = 41;
    const APPLICATION_ID_INVALID        = 42;

    /** Other errors */
    const INVALID_IP_ADDRESS            = 51;
    const REQUEST_LIMIT_EXCEEDED        = 52;
    const SOURCE_NOT_AVAILABLE          = 53;

    const SEARCH_NOT_SPECIFIED          = 71;
    const SEARCH_LENGTH_NOT_ENOUGH      = 72;

    const PAGE_NUMBER_INVALID           = 81;

    /**
     * @var string
     */
    private $field;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Construct
     *
     * @param string $message
     * @param int    $code
     * @param string $field
     * @param mixed  $value
     * @param \Exception $previous
     */
    public function __construct($message, $code, $field, $value, \Exception $previous = null)
    {
        $this->field = $field;
        $this->value = $value;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Create with WarGaming response
     *
     * @param array $errorInfo
     *
     * @return RequestErrorException
     */
    public static function createFromWarGamingResponse(array $errorInfo)
    {
        $code = self::UNDEFINED;
        $message = 'Undefined';

        // Add keys to array (For safe)
        $errorInfo += array(
            'field' => null,
            'message' => null,
            'value' => null
        );

        if (!empty($errorInfo['code'])) {
            switch ($errorInfo['code']) {
                case 402:
                    if ($errorInfo['message'] == 'SEARCH_NOT_SPECIFIED') {
                        $code = self::SEARCH_NOT_SPECIFIED;
                        $message = 'Search not specified';
                    } else {
                        $code = self::FIELD_NOT_SPECIFIED;
                        $message = sprintf('The field "%s" not specified.', $errorInfo['field']);
                    }

                    break;

                case 404:
                    if ($errorInfo['message'] == 'METHOD_NOT_FOUND') {
                        $code = self::METHOD_NOT_FOUND;
                        $message = sprintf('The method "%s" not found.', $errorInfo['value']);
                    } else {
                        $code = self::FIELD_NOT_FOUND;
                        $message = sprintf('The field "%s" not found.', $errorInfo['field']); // or value?
                    }

                    break;

                case 407:
                    if ($errorInfo['message'] == 'APPLICATION_IS_BLOCKED') {
                        $code = self::APPLICATION_IS_BLOCKED;
                        $message = 'Application is blocked.';

                    } else if ($errorInfo['message'] == 'INVALID_APPLICATION_ID') {
                        $code = self::APPLICATION_ID_INVALID;
                        $message = 'Invalid Application ID.';

                    } else if ($errorInfo['message'] == 'INVALID_IP_ADDRESS') {
                        $code = self::INVALID_IP_ADDRESS;
                        $message = 'Invalid IP Address.';

                    } else if ($errorInfo['message'] == 'REQUEST_LIMIT_EXCEEDED') {
                        $code = self::REQUEST_LIMIT_EXCEEDED;
                        $message = 'Request limit exceeded.';

                    } else if ($errorInfo['message'] == 'NOT_ENOUGH_SEARCH_LENGTH') {
                        $code = self::SEARCH_LENGTH_NOT_ENOUGH;
                        $message = 'Not enough search length.';

                    } else if ($errorInfo['message'] == 'INVALID_PAGE_NO') {
                        $code = self::PAGE_NUMBER_INVALID;
                        $message = 'Invalid page number parameter.';

                    } else if (strpos($errorInfo['message'], 'INVALID_') === 0) {
                        $code = self::FIELD_INVALID;
                        $message = sprintf(
                            'The value "%s" for field "%s" is invalid.',
                            $errorInfo['value'],
                            $errorInfo['field']
                        );

                    } else if (strpos($errorInfo['message'], '_LIST_LIMIT_EXCEEDED') !== false) {
                        $code = self::FIELD_LIST_LIMIT_EXCEEDED;
                        $message = 'Field list limit exceeded.';

                    }

                    break;

                case 504:
                    $code = self::SOURCE_NOT_AVAILABLE;
                    $message = 'Source not available';

                    break;
            }
        }

        return new static($message, $code, $errorInfo['value'], $errorInfo['field']);
    }
}

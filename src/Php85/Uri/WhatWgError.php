<?php

namespace Symfony\Polyfill\Php85\Uri;

/**
 * @author Alexandre Daubois <alex.daubois@gmail.com>
 *
 * @internal
 */
class WhatWgError
{
    public const ERROR_TYPE_DOMAIN_TO_ASCII = 0;
    public const ERROR_TYPE_DOMAIN_TO_UNICODE = 1;
    public const ERROR_TYPE_DOMAIN_INVALID_CODE_POINT = 2;
    public const ERROR_TYPE_HOST_INVALID_CODE_POINT = 3;
    public const ERROR_TYPE_IPV4_EMPTY_PART = 4;
    public const ERROR_TYPE_IPV4_TOO_MANY_PARTS = 5;
    public const ERROR_TYPE_IPV4_NON_NUMERIC_PART = 6;
    public const ERROR_TYPE_IPV4_NON_DECIMAL_PART = 7;
    public const ERROR_TYPE_IPV4_OUT_OF_RANGE_PART = 8;
    public const ERROR_TYPE_IPV6_UNCLOSED = 9;
    public const ERROR_TYPE_IPV6_INVALID_COMPRESSION = 10;
    public const ERROR_TYPE_IPV6_TOO_MANY_PIECES = 11;
    public const ERROR_TYPE_IPV6_MULTIPLE_COMPRESSION = 12;
    public const ERROR_TYPE_IPV6_INVALID_CODE_POINT = 13;
    public const ERROR_TYPE_IPV6_TOO_FEW_PIECES = 14;
    public const ERROR_TYPE_IPV4_IN_IPV6_TOO_MANY_PIECES = 15;
    public const ERROR_TYPE_IPV4_IN_IPV6_INVALID_CODE_POINT = 16;
    public const ERROR_TYPE_IPV4_IN_IPV6_OUT_OF_RANGE_PART = 17;
    public const ERROR_TYPE_IPV4_IN_IPV6_TOO_FEW_PARTS = 18;
    public const ERROR_TYPE_INVALID_URL_UNIT = 19;
    public const ERROR_TYPE_SPECIAL_SCHEME_MISSING_FOLLOWING_SOLIDUS = 20;
    public const ERROR_TYPE_MISSING_SCHEME_NON_RELATIVE_URL = 21;
    public const ERROR_TYPE_INVALID_REVERSE_SOLIDUS = 22;
    public const ERROR_TYPE_INVALID_CREDENTIALS = 23;
    public const ERROR_TYPE_HOST_MISSING = 24;
    public const ERROR_TYPE_PORT_OUT_OF_RANGE = 25;
    public const ERROR_TYPE_PORT_INVALID = 26;
    public const ERROR_TYPE_FILE_INVALID_WINDOWS_DRIVE_LETTER = 27;
    public const ERROR_TYPE_FILE_INVALID_WINDOWS_DRIVE_LETTER_HOST = 28;

    /**
     * @var string
     */
    public $position;

    /**
     * @var int
     */
    public $errorCode;

    public function __construct(string $position, int $errorCode)
    {
        $this->position = $position;
        $this->errorCode = $errorCode;
    }
}
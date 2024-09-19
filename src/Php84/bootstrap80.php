<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Php84 as p;

if (extension_loaded('mbstring')) {
    if (!function_exists('mb_ucfirst')) {
        function mb_ucfirst(string $string, ?string $encoding = null): string { return p\Php84::mb_ucfirst($string, $encoding); }
    }

    if (!function_exists('mb_lcfirst')) {
        function mb_lcfirst(string $string, ?string $encoding = null): string { return p\Php84::mb_lcfirst($string, $encoding); }
    }

    if (!function_exists('mb_trim')) {
        function mb_trim(string $string, ?string $characters = null, ?string $encoding = null): string { return p\Php84::mb_trim($string, $characters, $encoding); }
    }

    if (!function_exists('mb_ltrim')) {
        function mb_ltrim(string $string, ?string $characters = null, ?string $encoding = null): string { return p\Php84::mb_ltrim($string, $characters, $encoding); }
    }

    if (!function_exists('mb_rtrim')) {
        function mb_rtrim(string $string, ?string $characters = null, ?string $encoding = null): string { return p\Php84::mb_rtrim($string, $characters, $encoding); }
    }
}

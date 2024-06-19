<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Tests\Php84;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Php84Test extends TestCase
{
    /**
     * @dataProvider ucFirstDataProvider
     */
    public function testMbUcFirst(string $string, string $expected): void
    {
        $this->assertSame($expected, mb_ucfirst($string));
    }

    /**
     * @dataProvider lcFirstDataProvider
     */
    public function testMbLcFirst(string $string, string $expected): void
    {
        $this->assertSame($expected, mb_lcfirst($string));
    }

    /**
     * @dataProvider arrayFindDataProvider
     */
    public function testArrayFind(array $array, callable $callback, $expected): void
    {
        $this->assertSame($expected, array_find($array, $callback));
    }

    /**
     * @dataProvider arrayFindKeyDataProvider
     */
    public function testArrayFindKey(array $array, callable $callback, $expected): void
    {
        $this->assertSame($expected, array_find_key($array, $callback));
    }

    /**
     * @dataProvider arrayAnyDataProvider
     */
    public function testArrayAny(array $array, callable $callback, bool $expected): void
    {
        $this->assertSame($expected, array_any($array, $callback));
    }

    /**
     * @dataProvider arrayAllDataProvider
     */
    public function testArrayAll(array $array, callable $callback, bool $expected): void
    {
        $this->assertSame($expected, array_all($array, $callback));
    }

    public static function ucFirstDataProvider(): array
    {
        return [
            ['', ''],
            ['test', 'Test'],
            ['TEST', 'TEST'],
            ['TesT', 'TesT'],
            ['ａｂ', 'Ａｂ'],
            ['ＡＢＳ', 'ＡＢＳ'],
            ['đắt quá!', 'Đắt quá!'],
            ['აბგ', 'აბგ'],
            ['ǉ', 'ǈ'],
            ["\u{01CA}", "\u{01CB}"],
            ["\u{01CA}\u{01CA}", "\u{01CB}\u{01CA}"],
            ['łámał', 'Łámał'],
            // Full case-mapping and case-folding that changes the length of the string only supported
            // in PHP > 7.3.
            ['ßst', \PHP_VERSION_ID < 70300 ? 'ßst' : 'Ssst'],
        ];
    }

    public static function lcFirstDataProvider(): array
    {
        return [
            ['', ''],
            ['test', 'test'],
            ['Test', 'test'],
            ['tEST', 'tEST'],
            ['Ａｂ', 'ａｂ'],
            ['ＡＢＳ', 'ａＢＳ'],
            ['Đắt quá!', 'đắt quá!'],
            ['აბგ', 'აბგ'],
            ['ǈ', \PHP_VERSION_ID < 70200 ? 'ǈ' : 'ǉ'],
            ["\u{01CB}", \PHP_VERSION_ID < 70200 ? "\u{01CB}" : "\u{01CC}"],
            ["\u{01CA}", "\u{01CC}"],
            ["\u{01CA}\u{01CA}", "\u{01CC}\u{01CA}"],
            ["\u{212A}\u{01CA}", "\u{006b}\u{01CA}"],
            ['ß', 'ß'],
        ];
    }

    public static function arrayFindDataProvider(): array
    {
        $callable = function ($value): bool {
            return \strlen($value) > 2;
        };

        $callableKey = function ($value, $key): bool {
            return is_numeric($key);
        };

        return [
            [[], $callable, null],
            [['a', 'aa', 'aaa', 'aaaa'], $callable, 'aaa'],
            [['a', 'aa'], $callable, null],
            [['a' => '1', 'b' => '12', 'c' => '123', 'd' => '1234'], $callable, '123'],
            [['a' => '1', 'b' => '12', 'c' => '123', 3 => '1234'], $callableKey, '1234'],
        ];
    }

    public static function arrayFindKeyDataProvider(): array
    {
        $callable = function ($value): bool {
            return \strlen($value) > 2;
        };

        $callableKey = function ($value, $key): bool {
            return is_numeric($key);
        };

        return [
            [[], $callable, null],
            [['a', 'aa', 'aaa', 'aaaa'], $callable, 2],
            [['a', 'aa'], $callable, null],
            [['a' => '1', 'b' => '12', 'c' => '123', 'd' => '1234'], $callable, 'c'],
            [['a' => '1', 'b' => '12', 'c' => '123', 3 => '1234'], $callableKey, 3],
        ];
    }

    public static function arrayAnyDataProvider(): array
    {
        $callable = function ($value): bool {
            return \strlen($value) > 2;
        };

        $callableKey = function ($value, $key): bool {
            return is_numeric($key);
        };

        return [
            [[], $callable, false],
            [['a', 'aa', 'aaa', 'aaaa'], $callable, true],
            [['a', 'aa'], $callable, false],
            [['a' => '1', 'b' => '12', 'c' => '123', 'd' => '1234'], $callable, true],
            [['a' => '1', 'b' => '12', 'c' => '123', 3 => '1234'], $callableKey, true],
            [['a' => '1', 'b' => '12', 'c' => '123', 'd' => '1234'], $callableKey, false],
        ];
    }

    public static function arrayAllDataProvider(): array
    {
        $callable = function ($value): bool {
            return \strlen($value) > 2;
        };

        $callableKey = function ($value, $key): bool {
            return is_numeric($key);
        };

        return [
            [[], $callable, true],
            [['a', 'aa', 'aaa', 'aaaa'], $callable, false],
            [['aaa', 'aaa'], $callable, true],
            [['a' => '1', 'b' => '12', 'c' => '123', 'd' => '1234'], $callable, false],
            [['a' => '1', 'b' => '12', 'c' => '123', 'd' => '1234'], $callableKey, false],
            [[1 => '1', 2 => '12', 3 => '123', 4 => '1234'], $callableKey, true],
        ];
    }
}

<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Tests\Php83;

use PHPUnit\Framework\TestCase;

class Php83Test extends TestCase
{
    /**
     * @covers \Symfony\Polyfill\Php83\Php83::json_validate
     *
     * @dataProvider jsonDataProvider
     */
    public function testJsonValidate(bool $valid, string $json, string $errorMessage = 'No error', int $depth = 512, int $options = 0)
    {
        $this->assertSame($valid, json_validate($json, $depth, $options));
        $this->assertSame($errorMessage, json_last_error_msg());
    }

    /**
     * @covers \Symfony\Polyfill\Php83\Php83::mb_str_pad
     *
     * @dataProvider paddingStringProvider
     * @dataProvider paddingEmojiProvider
     * @dataProvider paddingEncodingProvider
     */
    public function testMbStrPad(string $expectedResult, string $string, int $length, string $padString, int $padType, ?string $encoding = null)
    {
        $this->assertSame($expectedResult, mb_convert_encoding(mb_str_pad($string, $length, $padString, $padType, $encoding), 'UTF-8', $encoding ?? mb_internal_encoding()));
    }

    /**
     * @covers \Symfony\Polyfill\Php83\Php83::mb_str_pad
     *
     * @dataProvider mbStrPadInvalidArgumentsProvider
     * @requires PHP 8
     */
    public function testMbStrPadInvalidArguments(string $expectedError, string $string, int $length, string $padString, int $padType, ?string $encoding = null)
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage($expectedError);

        mb_str_pad($string, $length, $padString, $padType, $encoding);
    }

    /**
     * @covers \Symfony\Polyfill\Php83\Php83::mb_str_pad
     *
     * @dataProvider mbStrPadInvalidArgumentsProvider
     * @requires PHP < 8
     */
    public function testMbStrPadInvalidArgumentsOnPhp7(string $expectedError, string $string, int $length, string $padString, int $padType, ?string $encoding = null)
    {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage($expectedError);

        set_error_handler(static function ($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr, $errno, $errfile, $errline);
        });

        try {
            mb_str_pad($string, $length, $padString, $padType, $encoding);
        } finally {
            restore_error_handler();
        }
    }

    public static function paddingStringProvider(): iterable
    {
        // Simple ASCII strings
        yield ['+Hello+', 'Hello', 7, '+-', \STR_PAD_BOTH];
        yield ['+-World+-+', 'World', 10, '+-', \STR_PAD_BOTH];
        yield ['+-Hello', 'Hello', 7, '+-', \STR_PAD_LEFT];
        yield ['+-+-+World', 'World', 10, '+-', \STR_PAD_LEFT];
        yield ['Hello+-', 'Hello', 7, '+-', \STR_PAD_RIGHT];
        yield ['World+-+-+', 'World', 10, '+-', \STR_PAD_RIGHT];
        // Edge cases pad length
        yield ['▶▶', '▶▶', 2, ' ', \STR_PAD_BOTH];
        yield ['▶▶', '▶▶', 1, ' ', \STR_PAD_BOTH];
        yield ['▶▶', '▶▶', 0, ' ', \STR_PAD_BOTH];
        yield ['▶▶', '▶▶', -1, ' ', \STR_PAD_BOTH];
        // Empty input string
        yield ['  ', '', 2, ' ', \STR_PAD_BOTH];
        yield [' ', '', 1, ' ', \STR_PAD_BOTH];
        yield ['', '', 0, ' ', \STR_PAD_BOTH];
        yield ['', '', -1, ' ', \STR_PAD_BOTH];
        // Default argument
        yield ['▶▶    ', '▶▶', 6, ' ', \STR_PAD_RIGHT];
        yield ['    ▶▶', '▶▶', 6, ' ', \STR_PAD_LEFT];
        yield ['  ▶▶  ', '▶▶', 6, ' ', \STR_PAD_BOTH];
    }

    public static function paddingEmojiProvider(): iterable
    {
        // UTF-8 Emojis
        yield ['▶▶❤❓❇❤', '▶▶', 6, '❤❓❇', \STR_PAD_RIGHT];
        yield ['❤❓❇❤▶▶', '▶▶', 6, '❤❓❇', \STR_PAD_LEFT];
        yield ['❤❓▶▶❤❓', '▶▶', 6, '❤❓❇', \STR_PAD_BOTH];
        yield ['▶▶❤❓❇', '▶▶', 5, '❤❓❇', \STR_PAD_RIGHT];
        yield ['❤❓❇▶▶', '▶▶', 5, '❤❓❇', \STR_PAD_LEFT];
        yield ['❤▶▶❤❓', '▶▶', 5, '❤❓❇', \STR_PAD_BOTH];
        yield ['▶▶❤❓', '▶▶', 4, '❤❓❇', \STR_PAD_RIGHT];
        yield ['❤❓▶▶', '▶▶', 4, '❤❓❇', \STR_PAD_LEFT];
        yield ['❤▶▶❤', '▶▶', 4, '❤❓❇', \STR_PAD_BOTH];
        yield ['▶▶❤', '▶▶', 3, '❤❓❇', \STR_PAD_RIGHT];
        yield ['❤▶▶', '▶▶', 3, '❤❓❇', \STR_PAD_LEFT];
        yield ['▶▶❤', '▶▶', 3, '❤❓❇', \STR_PAD_BOTH];

        for ($i = 2; $i >= 0; --$i) {
            yield ['▶▶', '▶▶', $i, '❤❓❇', \STR_PAD_RIGHT];
            yield ['▶▶', '▶▶', $i, '❤❓❇', \STR_PAD_LEFT];
            yield ['▶▶', '▶▶', $i, '❤❓❇', \STR_PAD_BOTH];
        }
    }

    public static function paddingEncodingProvider(): iterable
    {
        $string = 'Σὲ γνωρίζω ἀπὸ τὴν κόψη Зарегистрируйтесь';

        foreach (['UTF-8', 'UTF-32', 'UTF-7'] as $encoding) {
            $input = mb_convert_encoding($string, $encoding, 'UTF-8');
            $padStr = mb_convert_encoding('▶▶', $encoding, 'UTF-8');

            yield ['Σὲ γνωρίζω ἀπὸ τὴν κόψη Зарегистрируйтесь▶▶▶', $input, 44, $padStr, \STR_PAD_RIGHT, $encoding];
            yield ['▶▶▶Σὲ γνωρίζω ἀπὸ τὴν κόψη Зарегистрируйтесь', $input, 44, $padStr, \STR_PAD_LEFT, $encoding];
            yield ['▶Σὲ γνωρίζω ἀπὸ τὴν κόψη Зарегистрируйтесь▶▶', $input, 44, $padStr, \STR_PAD_BOTH, $encoding];
        }
    }

    public static function mbStrPadInvalidArgumentsProvider(): iterable
    {
        yield ['mb_str_pad(): Argument #3 ($pad_string)', '▶▶', 6, '', \STR_PAD_RIGHT];
        yield ['mb_str_pad(): Argument #3 ($pad_string)', '▶▶', 6, '', \STR_PAD_LEFT];
        yield ['mb_str_pad(): Argument #3 ($pad_string)', '▶▶', 6, '', \STR_PAD_BOTH];
        yield ['mb_str_pad(): Argument #4 ($pad_type) must be STR_PAD_LEFT, STR_PAD_RIGHT, or STR_PAD_BOTH', '▶▶', 6, ' ', 123456];
        yield ['mb_str_pad(): Argument #5 ($encoding) must be a valid encoding, "unexisting" given', '▶▶', 6, ' ', \STR_PAD_BOTH, 'unexisting'];
    }

    /**
     * @return iterable<array{0: bool, 1: string, 2?: string, 3?: int, 4?: int}>
     */
    public static function jsonDataProvider(): iterable
    {
        yield [false, '', 'Syntax error'];
        yield [false, '.', 'Syntax error'];
        yield [false, '<?>', 'Syntax error'];
        yield [false, ';', 'Syntax error'];
        yield [false, 'руссиш', 'Syntax error'];
        yield [false, 'blah', 'Syntax error'];
        yield [false, '{ "": "": "" } }', 'Syntax error'];
        yield [false, '{ "test": {} "foo": "bar" }, "test2": {"foo" : "bar" }, "test2": {"foo" : "bar" } }', 'Syntax error'];
        yield [true, '{ "test": { "foo": "bar" } }'];
        yield [true, '{ "test": { "foo": "" } }'];
        yield [true, '{ "": { "foo": "" } }'];
        yield [true, '{ "": { "": "" } }'];
        yield [true, '{ "test": {"foo": "bar"}, "test2": {"foo" : "bar" }, "test2": {"foo" : "bar" } }'];
        yield [true, '{ "test": {"foo": "bar"}, "test2": {"foo" : "bar" }, "test3": {"foo" : "bar" } }'];
        yield [false, '{"key1":"value1", "key2":"value2"}', 'Maximum stack depth exceeded', 1];
        yield [false, "\"a\xb0b\"", 'Malformed UTF-8 characters, possibly incorrectly encoded'];
        yield [true, '{ "test": { "foo": "bar" } }', 'No error', 2147483647];

        if (\defined('JSON_INVALID_UTF8_IGNORE')) {
            yield [true, "\"a\xb0b\"", 'No error', 512, \JSON_INVALID_UTF8_IGNORE];
        } else {
            // The $options should not be validated when JSON_INVALID_UTF8_IGNORE is not defined (PHP 7.1)
            yield [true, '{}', 'No error', 512, 1];
        }
    }

    /**
     * @covers \Symfony\Polyfill\Php83\Php83::json_validate
     *
     * @dataProvider jsonInvalidOptionsProvider
     */
    public function testJsonValidateInvalidOptionsProvided(int $depth, int $flags, string $expectedError)
    {
        $this->expectException(\ValueError::class);
        $this->expectErrorMessage($expectedError);
        json_validate('{}', $depth, $flags);
    }

    /**
     * @return iterable<array{0: int, 1: int, 2: string}>
     */
    public static function jsonInvalidOptionsProvider(): iterable
    {
        yield [0, 0, 'json_validate(): Argument #2 ($depth) must be greater than 0'];
        if (\PHP_INT_MAX > 2147483647) {
            yield [\PHP_INT_MAX, 0, 'json_validate(): Argument #2 ($depth) must be less than 2147483647'];
        }
        if (\defined('JSON_INVALID_UTF8_IGNORE')) {
            yield [
                512,
                \JSON_BIGINT_AS_STRING,
                'json_validate(): Argument #3 ($flags) must be a valid flag (allowed flags: JSON_INVALID_UTF8_IGNORE)',
            ];
        }
    }

    public function testStreamContextSetOptions()
    {
        $context = stream_context_create();
        $this->assertTrue(stream_context_set_options($context, ['http' => ['method' => 'POST']]));
        $this->assertSame(['http' => ['method' => 'POST']], stream_context_get_options($context));
    }

    public function testDateTimeExceptionClassesExist()
    {
        $this->assertTrue(class_exists(\DateError::class));
        $this->assertTrue(class_exists(\DateObjectError::class));
        $this->assertTrue(class_exists(\DateRangeError::class));
        $this->assertTrue(class_exists(\DateException::class));
        $this->assertTrue(class_exists(\DateInvalidTimeZoneException::class));
        $this->assertTrue(class_exists(\DateInvalidOperationException::class));
        $this->assertTrue(class_exists(\DateMalformedStringException::class));
        $this->assertTrue(class_exists(\DateMalformedIntervalStringException::class));
        $this->assertTrue(class_exists(\DateMalformedPeriodStringException::class));
    }

    /**
     * @covers \Symfony\Polyfill\Php83\Php83::str_increment
     *
     * @dataProvider strIncrementProvider
     */
    public function testStrIncrement(string $result, string $string)
    {
        $this->assertSame($result, str_increment($string));
    }

    /**
     * @covers \Symfony\Polyfill\Php83\Php83::str_decrement
     *
     * @dataProvider strDecrementProvider
     */
    public function testStrDecrement(string $result, string $string)
    {
        $this->assertSame($result, str_decrement($string));
    }

    public static function strIncrementProvider(): iterable
    {
        yield ['ABD', 'ABC'];
        yield ['EB', 'EA'];
        yield ['AAA', 'ZZ'];
        yield ['Ba', 'Az'];
        yield ['bA', 'aZ'];
        yield ['B0', 'A9'];
        yield ['b0', 'a9'];
        yield ['AAa', 'Zz'];
        yield ['aaA', 'zZ'];
        yield ['10a', '9z'];
        yield ['10A', '9Z'];
        yield ['5e7', '5e6'];
        yield ['e', 'd'];
        yield ['E', 'D'];
        yield ['5', '4'];
    }

    public static function strDecrementProvider(): iterable
    {
        yield ['Ay', 'Az'];
        yield ['aY', 'aZ'];
        yield ['A8', 'A9'];
        yield ['a8', 'a9'];
        yield ['Yz', 'Za'];
        yield ['yZ', 'zA'];
        yield ['Y9', 'Z0'];
        yield ['y9', 'z0'];
        yield ['Z', 'aA'];
        yield ['9', 'A0'];
        yield ['9', 'a0'];
        yield ['9', '10'];
        yield ['Z', '1A'];
        yield ['z', '1a'];
        yield ['9z', '10a'];
        yield ['5e5', '5e6'];
        yield ['C', 'D'];
        yield ['c', 'd'];
        yield ['3', '4'];
    }

    /**
     * @covers \Symfony\Polyfill\Php83\Php83::str_increment
     *
     * @dataProvider strInvalidIncrementProvider
     */
    public function testInvalidStrIncrement(string $errorMessage, string $string)
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage($errorMessage);

        str_increment($string);
    }

    public static function strInvalidIncrementProvider(): iterable
    {
        yield ['str_increment(): Argument #1 ($string)', ''];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', '-cc'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', 'Z '];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', ' Z'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', 'é'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', '我喜歡雞肉'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', 'α'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', 'ω'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', 'Α'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', 'Ω'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', 'foo1.txt'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', '1f.5'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', 'foo.1.txt'];
        yield ['str_increment(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', '1.f.5'];
    }

    /**
     * @covers \Symfony\Polyfill\Php83\Php83::str_decrement
     *
     * @dataProvider strInvalidDecrementProvider
     */
    public function testInvalidStrDecrement(string $errorMessage, string $string)
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage($errorMessage);

        str_decrement($string);
    }

    public static function strInvalidDecrementProvider(): iterable
    {
        yield ['str_decrement(): Argument #1 ($string)', ''];
        yield ['str_decrement(): Argument #1 ($string) must be composed only of alphanumeric ASCII characters', '我喜歡雞肉'];
        yield ['str_decrement(): Argument #1 ($string) "0" is out of decrement range', '0'];
        yield ['str_decrement(): Argument #1 ($string) "a" is out of decrement range', 'a'];
        yield ['str_decrement(): Argument #1 ($string) "A" is out of decrement range', 'A'];
        yield ['str_decrement(): Argument #1 ($string) "00" is out of decrement range', '00'];
        yield ['str_decrement(): Argument #1 ($string) "0a" is out of decrement range', '0a'];
        yield ['str_decrement(): Argument #1 ($string) "0A" is out of decrement range', '0A'];
    }
}

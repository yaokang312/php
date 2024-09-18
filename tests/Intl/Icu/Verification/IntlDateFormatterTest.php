<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Tests\Intl\Icu\Verification;

use Symfony\Polyfill\Intl\Icu\IntlDateFormatter;
use Symfony\Polyfill\Tests\Intl\Icu\AbstractIntlDateFormatterTest;

/**
 * Verifies that {@link AbstractIntlDateFormatterTest} matches the behavior of
 * the {@link \IntlDateFormatter} class in a specific version of ICU.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @requires extension intl
 *
 * @group class-polyfill
 */
class IntlDateFormatterTest extends AbstractIntlDateFormatterTest
{
    /**
     * @dataProvider formatTimezoneProvider
     */
    public function testFormatTimezone($pattern, $timezone, $expected)
    {
        if (version_compare(\INTL_ICU_VERSION, '59.1', '<')) {
            $this->markTestSkipped('ICU version 59.1 is required.');
        }

        parent::testFormatTimezone($pattern, $timezone, $expected);
    }

    public function testFormatUtcAndGmtAreSplit()
    {
        if (version_compare(\INTL_ICU_VERSION, '59.1', '<')) {
            $this->markTestSkipped('ICU version 59.1 is required.');
        }

        parent::testFormatUtcAndGmtAreSplit();
    }

    /**
     * @dataProvider dateAndTimeTypeProvider
     */
    public function testDateAndTimeType($timestamp, $datetype, $timetype, $expected)
    {
        if (version_compare(\INTL_ICU_VERSION, '59.1', '<')) {
            $this->markTestSkipped('ICU version 59.1 is required.');
        }

        parent::testDateAndTimeType($timestamp, $datetype, $timetype, $expected);
    }

    /**
     * @dataProvider relativeDateTypeProvider
     */
    public function testRelativeDateType($timestamp, $datetype, $timetype, $expected)
    {
        if (version_compare(\INTL_ICU_VERSION, '59.1', '<')) {
            $this->markTestSkipped('ICU version 59.1 is required.');
        }

        parent::testRelativeDateType($timestamp, $datetype, $timetype, $expected);
    }

    public function testFormatIgnoresPatternForRelativeDateType()
    {
        if (version_compare(\INTL_ICU_VERSION, '59.1', '<')) {
            $this->markTestSkipped('ICU version 59.1 is required.');
        }

        parent::testFormatIgnoresPatternForRelativeDateType();
    }

    protected function getDateFormatter($locale, $datetype, $timetype, $timezone = null, $calendar = IntlDateFormatter::GREGORIAN, $pattern = null)
    {
        if (version_compare(\INTL_ICU_VERSION, '55.1', '<')) {
            $this->markTestSkipped('ICU version 55.1 is required.');
        }

        if (!$formatter = new \IntlDateFormatter($locale, $datetype, $timetype, $timezone, $calendar, $pattern)) {
            throw new \InvalidArgumentException(intl_get_error_message());
        }

        return $formatter;
    }

    protected function getIntlErrorMessage(): string
    {
        return intl_get_error_message();
    }

    protected function getIntlErrorCode(): int
    {
        return intl_get_error_code();
    }

    protected function isIntlFailure($errorCode): bool
    {
        return intl_is_failure($errorCode);
    }
}

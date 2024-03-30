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

use PHPUnit\Framework\TestCase;
use Symfony\Polyfill\Php84\Php84 as p;

class Php84Test extends TestCase
{
    public function test_mb_lcfirst(): void
    {
        $this->assertSame('', p::mb_lcfirst('', 'UTF-8'));
        $this->assertSame('ａＢＳ', p::mb_lcfirst('ＡＢＳ', 'UTF-8'));
        $this->assertSame('xin chào', p::mb_lcfirst('Xin chào', 'UTF-8'));
        $this->assertSame('đẹp quá!', p::mb_lcfirst('Đẹp quá!', 'UTF-8'));
    }

    public function test_mb_ucfirst(): void
    {
        $this->assertSame('', p::mb_ucfirst('', 'UTF-8'));
        $this->assertSame('Ａｂ', p::mb_ucfirst('ａｂ', 'UTF-8'));
        $this->assertSame('ＡＢＳ', p::mb_ucfirst('ＡＢＳ', 'UTF-8'));
        $this->assertSame('Đắt quá!', p::mb_ucfirst('đắt quá!', 'UTF-8'));
        $this->assertSame('აბგ', p::mb_ucfirst('აბგ', 'UTF-8'));
        $this->assertSame('ǈ', p::mb_ucfirst('ǉ', 'UTF-8'));
    }
}

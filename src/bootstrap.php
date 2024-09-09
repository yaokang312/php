<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (\PHP_VERSION_ID < 80200) {
    require __DIR__.'/Php82/bootstrap.php';
}

if (\PHP_VERSION_ID < 80300) {
    require __DIR__.'/Php83/bootstrap.php';
}

if (\PHP_VERSION_ID < 80400) {
    require __DIR__.'/Php84/bootstrap.php';
}

<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Php85\Uri;

/**
 * @author Alexandre Daubois <alex.daubois@gmail.com>
 *
 * @internal
 */
abstract class Uri implements \Stringable
{
    protected const URI_GLOBAL_REGEX = '/^(?:(?P<scheme>[^:\/?#]+):)?(?:\/\/(?P<authority>[^\/?#]*))?(?P<path>[^?#]*)(?:\?(?P<query>[^#]*))?(?:#(?P<fragment>.*))?$/';
    protected const URI_AUTHORITY_REGEX = '/^(?:(?P<user>[^:@]*)(?::(?P<pass>[^@]*))?@)?(?P<host>[^:]*)(?::(?P<port>\d*))?$/';
    protected $initialized = false;

    /**
     * @var string|null
     */
    protected $scheme;

    /**
     * @var string|null
     */
    protected $user;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $host;

    /**
     * @var int|null
     */
    protected $port;

    /**
     * @var string|null
     */
    protected $path;

    /**
     * @var string|null
     */
    protected $query;

    /**
     * @var string|null
     */
    protected $fragment;

    public static function fromRfc3986(string $uri, ?string $baseUrl = null): ?static
    {
        try {
            return new Rfc3986Uri($uri, $baseUrl);
        } catch (\ValueError $error) {
            throw $error;
        } catch (\Error $error) {
            return null;
        }
    }

    /**
     * @param array<int, WhatWgError> $errors
     */
    public static function fromWhatWg(string $uri, ?string $baseUrl = null, &$errors = null): ?static
    {
        $uri = new WhatWgUri($uri, $baseUrl, $errors);

        if ($errors) {
            return null;
        }

        return $uri;
    }

    public function getScheme(): ?string
    {
        $this->ensureInitialized();

        return $this->scheme;
    }

    public function getUser(): ?string
    {
        $this->ensureInitialized();

        return $this->user;
    }

    public function getPassword(): ?string
    {
        $this->ensureInitialized();

        return $this->password;
    }

    public function getHost(): ?string
    {
        $this->ensureInitialized();

        return $this->host;
    }

    public function getPort(): ?int
    {
        $this->ensureInitialized();

        return $this->port;
    }

    public function getPath(): ?string
    {
        $this->ensureInitialized();

        return $this->path;
    }

    public function getQuery(): ?string
    {
        $this->ensureInitialized();

        return $this->query;
    }

    public function getFragment(): ?string
    {
        $this->ensureInitialized();

        return $this->fragment;
    }

    private function ensureInitialized(): void
    {
        if (!$this->initialized) {
            throw new \Error(\sprintf('%s object is not correctly initialized', static::class));
        }
    }
}

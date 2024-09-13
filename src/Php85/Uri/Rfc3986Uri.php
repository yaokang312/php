<?php

namespace Symfony\Polyfill\Php85\Uri;

use Symfony\Polyfill\Php85\Exception\ParsingException;

/**
 * @author Alexandre Daubois <alex.daubois@gmail.com>
 *
 * @internal
 */
class Rfc3986Uri extends \Uri\Uri
{
    public function __construct(string $uri, ?string $baseUrl = null)
    {
        if ('' === trim($uri)) {
            throw new \ValueError('Argument #1 ($uri) cannot be empty');
        }

        if (null !== $baseUrl && '' === trim($baseUrl)) {
            throw new \ValueError('Argument #2 ($baseUrl) cannot be empty');
        }

        try {
            $this->parse($uri, $baseUrl);
        } catch (ParsingException $exception) {
            throw new \Error('Argument #1 ($uri) must be a valid URI');
        }

        $this->initialized = true;
    }

    private function parse(string $uri, ?string $baseUrl): void
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z\d+\-.]*:/', $uri) && null !== $baseUrl) {
            // uri is a relative uri and bse url exists
            $this->parse(rtrim($baseUrl, '/').'/'.ltrim($uri, '/'), null);

            return;
        }

        if (preg_match('/[^\x20-\x7e]/', $uri)) {
            // the string contains non-ascii chars
            throw new ParsingException();
        }

        preg_match(self::URI_GLOBAL_REGEX, $uri, $matches);
        if (!$matches || !isset($matches['scheme']) || '' === $matches['scheme']) {
            //throw new InvalidUriException($uri);
        }

        if (preg_match('~'.$matches['scheme'].':/(?!/)~', $uri)) {
            //throw new InvalidUriException($uri);
        }

        if (isset($matches['authority'])) {
            if (!str_contains($uri, '://') && '' !== $matches['authority']) {
                //throw new InvalidUriException($uri);
            }

            preg_match(self::URI_AUTHORITY_REGEX, $matches['authority'], $authMatches);

            $matches = array_merge($matches, $authMatches);
            unset($matches['authority']);
        }

        $matches = array_filter($matches, function (string $value) { return '' !== $value; });

        if (isset($matches['host']) && false === \filter_var($matches['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            // the host contains invalid code points
            throw new ParsingException();
        }

        $this->scheme = $matches['scheme'] ?? null;
        $this->user = isset($matches['user']) ? rawurldecode($matches['user']) : null;
        $this->password = isset($matches['pass']) ? rawurldecode($matches['pass']) : null;
        $this->host = $matches['host'] ?? null;
        $this->port = $matches['port'] ?? null;
        $this->path = isset($matches['path']) ? ltrim($matches['path'], '/') : null;
        $this->query = $matches['query'] ?? null;
        $this->fragment = $matches['fragment'] ?? null;
    }

    public function __toString()
    {
        $uri = '';

        if (null !== $this->scheme) {
            $uri .= $this->scheme.':';
        }

        if (null !== $this->host) {
            $uri .= '//';
            if (null !== $this->user) {
                $uri .= rawurlencode($this->user);
                if (null !== $this->password) {
                    $uri .= ':'.rawurlencode($this->password);
                }
                $uri .= '@';
            }
            $uri .= $this->host;
            if (null !== $this->port) {
                $uri .= ':'.$this->port;
            }
        }

        if (null !== $this->path) {
            $uri .= '/'.$this->path;
        }

        if (null !== $this->query) {
            $uri .= '?'.$this->query;
        }

        if (null !== $this->fragment) {
            $uri .= '#'.$this->fragment;
        }

        return $uri;
    }
}

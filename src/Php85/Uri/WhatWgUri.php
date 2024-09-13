<?php

namespace Symfony\Polyfill\Php85\Uri;

/**
 * @author Alexandre Daubois <alex.daubois@gmail.com>
 *
 * @internal
 */
class WhatWgUri extends \Uri\Uri
{
    /**
     * @param array<int, WhatWgError> $errors
     */
    public function __construct(string $uri, ?string $baseUrl = null, &$errors = null)
    {
        if ('' === trim($uri)) {
            throw new \ValueError('Argument #1 ($uri) cannot be empty');
        }

        if (null !== $baseUrl && '' === trim($baseUrl)) {
            throw new \ValueError('Argument #2 ($baseUrl) cannot be empty');
        }

        $this->parse($uri, $baseUrl, $errors);

        $this->initialized = true;
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
            $uri .= idn_to_utf8($this->host);
            if (null !== $this->port) {
                $uri .= ':'.$this->port;
            }
        }

        $uri .= '/';
        if (null !== $this->path) {
            $uri .= $this->path;
        }

        if (null !== $this->query) {
            $uri .= '?'.$this->query;
        }

        if (null !== $this->fragment) {
            $uri .= '#'.$this->fragment;
        }

        return $uri;
    }

    private function parse(string $uri, ?string $baseUrl, &$errors = null): void
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z\d+\-.]*:/', $uri) && null !== $baseUrl) {
            // uri is a relative uri and bse url exists
            $this->parse(rtrim($baseUrl, '/').'/'.ltrim($uri, '/'), null);

            return;
        }

        preg_match(self::URI_GLOBAL_REGEX, $uri, $matches);
        if (!$matches || !isset($matches['scheme']) || '' === $matches['scheme']) {
            //throw new InvalidUriException($uri);
            $errors[] = new WhatWgError($uri, WhatWgError::ERROR_TYPE_MISSING_SCHEME_NON_RELATIVE_URL);

            return;
        }

        if (preg_match('~'.$matches['scheme'].':/(?!/)~', $uri)) {
            $errors[] = new WhatWgError($uri, WhatWgError::ERROR_TYPE_SPECIAL_SCHEME_MISSING_FOLLOWING_SOLIDUS);

            return;
        }

        if (isset($matches['authority'])) {
            if (!str_contains($uri, '://') && '' !== $matches['authority']) {
                //throw new InvalidUriException($uri);
                return;
            }

            preg_match(self::URI_AUTHORITY_REGEX, $matches['authority'], $authMatches);

            $matches = array_merge($matches, $authMatches);
            unset($matches['authority']);
        }

        $matches = array_filter($matches, function (string $value) { return '' !== $value; });

        $host = null;
        if (isset($matches['host'])) {
            $host = $this->handleHost($matches['host'], $errors);
        }

        $path = null;
        if (isset($matches['path'])) {
            $path = $this->resolvePath($this->handlePath(ltrim($matches['path'], '/')));
        }

        $this->scheme = isset($matches['scheme']) ? strtolower($matches['scheme']) : null;
        $this->user = isset($matches['user']) ? rawurldecode($matches['user']) : null;
        $this->password = isset($matches['pass']) ? rawurldecode($matches['pass']) : null;
        $this->host = $host;
        $this->port = $matches['port'] ?? null;
        $this->path = $path;
        $this->query = isset($matches['query']) ? $this->encodeQueryParams($matches['query']) : null;
        $this->fragment = $matches['fragment'] ?? null;
    }

    private function handleHost(string $host, &$errors): string
    {
        $host = strtolower($host);

        // validate hostname and ensure there isn't any invalid code point
        if ($host === idn_to_ascii($host) && false === filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            $errors[] = new WhatWgError($host, WhatWgError::ERROR_TYPE_DOMAIN_INVALID_CODE_POINT);
        }

        // check if host is an IP address using octal notation
        if (preg_match('/^(0[0-7]+\.){3}(0[0-7]+)$/', $host)) {
            // decode each part, remove leading zeros and re-encode in reverse order
            $host = \explode('.', $host);
            $host = array_map(function ($part) {
                $part = ltrim($part, '0');

                return (string) octdec($part);
            }, array_reverse($host));

            $host = implode('.', $host);
        }

        return idn_to_ascii($host);
    }

    private function resolvePath(string $path): string
    {
        $relativeParts = explode('/', $path);
        $resolvedPathSegments = [];

        foreach ($relativeParts as $segment) {
            if ('..' === $segment) {
                array_pop($resolvedPathSegments);
            } elseif ('.' !== $segment && '' !== $segment) {
                $resolvedPathSegments[] = $segment;
            }
        }

        return implode('/', $resolvedPathSegments);
    }

    private function handlePath(string $path): string
    {
        // check if the path looks like a Windows path
        if (preg_match('/^(?P<drive>[a-zA-Z]):[\\/\\\\](?P<path>.*)/', $path, $matches)) {
            $filePath = ltrim(str_replace('\\', '/', $matches['path']), '/');
            $path = $matches['drive'].':/'.rawurlencode($filePath);
        }

        return $path;
    }

    private function encodeQueryParams(string $query): string
    {
        $queryParts = explode('&', $query);
        $encodedQueryParts = [];

        foreach ($queryParts as $queryPart) {
            $queryPartParts = explode('=', $queryPart, 2);
            $encodedQueryParts[] = rawurlencode($queryPartParts[0]).(isset($queryPartParts[1]) ? '='.urlencode(urldecode($queryPartParts[1])) : '');
        }

        return implode('&', $encodedQueryParts);
    }
}
<?php

namespace Symfony\Polyfill\Tests\Php85;

use PHPUnit\Framework\TestCase;
use Symfony\Polyfill\Php85\Uri\Uri;
use Symfony\Polyfill\Php85\Uri\WhatWgError;
use Symfony\Polyfill\Php85\Uri\WhatWgUri;

/**
 * @covers \Symfony\Polyfill\Php85\Uri\WhatWgUri
 */
class WhatWgUriTest extends TestCase
{
    public function testParseSimpleUri()
    {
        $whatwg = Uri::fromWhatWg('https://example.com/');

        $this->assertSame('https', $whatwg->getScheme());
        $this->assertSame('example.com', $whatwg->getHost());
    }

    public function testParseUrlWithElements()
    {
        $uri = Uri::fromWhatWg('https://username:password@example.com:8080/pathname1/pathname2/pathname3?query=true#hash-exists');

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame('pathname1/pathname2/pathname3', $uri->getPath());
        $this->assertSame('query=true', $uri->getQuery());
        $this->assertSame('hash-exists', $uri->getFragment());
    }

    public function testParseExoticUrl()
    {
        $uri = Uri::fromWhatWg('http://username:password@héééostname:9090/gah/../path?arg=vaéue#anchor');

        $this->assertSame('http', $uri->getScheme());
        $this->assertSame('xn--hostname-b1aaa', $uri->getHost());
        $this->assertSame(9090, $uri->getPort());
        $this->assertSame('path', $uri->getPath());
        $this->assertSame('arg=va%C3%A9ue', $uri->getQuery());
        $this->assertSame('anchor', $uri->getFragment());
    }

    public function testParsePath()
    {
        $this->assertNull(Uri::fromWhatWg("/page:1"));
    }

    public function testEmptyUrl()
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('Argument #1 ($uri) cannot be empty');

        Uri::fromWhatWg('');
    }

    public function testEmptyBaseUrl()
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('Argument #2 ($baseUrl) cannot be empty');

        Uri::fromWhatWg('https://example.com/', '');
    }

    public function testParseInvalidUrl()
    {
        $errors = [];
        $uri = Uri::fromWhatWg('192.168/contact.html', null, $errors);

        $this->assertNull($uri);
        $this->assertSame('192.168/contact.html', $errors[0]->position);
        $this->assertSame(WhatWgError::ERROR_TYPE_MISSING_SCHEME_NON_RELATIVE_URL, $errors[0]->errorCode);
    }

    public function testInvalidCodePointInDomain()
    {
        $errors = [];
        $uri = Uri::fromWhatWg("http://RuPaul's Drag Race All Stars 7 Winners Cast on This Season's", null, $errors);

        $this->assertNull($uri);
        $this->assertSame("rupaul's drag race all stars 7 winners cast on this season's", $errors[0]->position);
        $this->assertSame(WhatWgError::ERROR_TYPE_DOMAIN_INVALID_CODE_POINT, $errors[0]->errorCode);
    }

    public function testConstructor()
    {
        $uri = new WhatWgUri("https://username:password@éxample.com:8080/path?q=r#fragment");

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('username', $uri->getUser());
        $this->assertSame('password', $uri->getPassword());
        $this->assertSame('xn--xample-9ua.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('path', $uri->getPath());
        $this->assertSame('q=r', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
    }

    public function testIanaScheme()
    {
        $uri = Uri::fromWhatWg("chrome-extension://example.com");

        $this->assertSame('chrome-extension', $uri->getScheme());
        $this->assertSame('example.com', $uri->getHost());

        $this->assertNull($uri->getUser());
        $this->assertNull($uri->getPassword());
        $this->assertNull($uri->getPort());
        $this->assertNull($uri->getPath());
        $this->assertNull($uri->getQuery());
        $this->assertNull($uri->getFragment());
    }

    public function testWithBaseUriAndAbsoluteUrl()
    {
        $uri = Uri::fromWhatWg("http://example.com/path/to/file2", "https://test.com");

        $this->assertSame('http', $uri->getScheme());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame('path/to/file2', $uri->getPath());
    }

    public function testWithBaseUriAndRelativeUrl()
    {
        $uri = Uri::fromWhatWg("/path/to/file2", "https://test.com");

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('test.com', $uri->getHost());
        $this->assertSame('path/to/file2', $uri->getPath());
    }

    public function testNormalization()
    {
        $uri = Uri::fromWhatWg("HttPs://0300.0250.0000.0001/path?query=foo%20bar");

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('1.0.168.192', $uri->getHost());
        $this->assertSame('path', $uri->getPath());
        $this->assertSame('query=foo+bar', $uri->getQuery());
    }

    public function testFileScheme()
    {
        $uri = Uri::fromWhatWg('file:///E:\\\\Documents and Settings');

        $this->assertSame('file', $uri->getScheme());
        $this->assertNull($uri->getHost());
        $this->assertSame('E:/Documents%20and%20Settings', $uri->getPath());
    }

    public function testInstantiateWithoutConstructor()
    {
        $r = new \ReflectionClass(WhatWgUri::class);
        $uri = $r->newInstanceWithoutConstructor();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Symfony\Polyfill\Php85\Uri\WhatWgUri object is not correctly initialized');

        $uri->getHost();
    }

    public function testToString()
    {
        $uri = Uri::fromWhatWg('http://user:pass@example.com?foo=Hell%C3%B3+W%C3%B6rld');

        $this->assertSame('http://user:pass@example.com/?foo=Hell%C3%B3+W%C3%B6rld', $uri->__toString());
    }
}

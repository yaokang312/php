<?php

namespace Symfony\Polyfill\Tests\Php85;

use PHPUnit\Framework\TestCase;
use Symfony\Polyfill\Php85\Uri\Rfc3986Uri;
use Symfony\Polyfill\Php85\Uri\Uri;

/**
 * @covers \Symfony\Polyfill\Php85\Uri\Rfc3986Uri
 */
class Rfc3986UriTest extends TestCase
{
    public function testParseSimpleUri()
    {
        $uri = Uri::fromRfc3986('https://example.com/');

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('example.com', $uri->getHost());
    }

    public function testParseUrlWithElements()
    {
        $uri = Uri::fromRfc3986('https://username:password@example.com:8080/pathname1/pathname2/pathname3?query=true#hash-exists');

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame('pathname1/pathname2/pathname3', $uri->getPath());
        $this->assertSame('query=true', $uri->getQuery());
        $this->assertSame('hash-exists', $uri->getFragment());
    }

    public function testParseExoticUrl()
    {
        $this->assertNull(Uri::fromRfc3986('http://username:password@héééostname:9090/gah/../path?arg=vaéue#anchor'));
    }

    public function testParsePath()
    {
        $uri = Uri::fromRfc3986('/page:1');

        $this->assertNull($uri->getScheme());
        $this->assertNull($uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertSame('page:1', $uri->getPath());
    }

    public function testEmptyUrl()
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('Argument #1 ($uri) cannot be empty');

        Uri::fromRfc3986('');
    }

    public function testEmptyBaseUrl()
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('Argument #2 ($baseUrl) cannot be empty');

        Uri::fromRfc3986('https://example.com', '');
    }

    public function testParseInvalidUrl()
    {
        $uri = Uri::fromRfc3986("192.168/contact.html");

        $this->assertSame('192.168/contact.html', $uri->getPath());
    }

    public function testInvalidCodePointInDomain()
    {
        $this->assertNull(Uri::fromRfc3986("http://RuPaul's Drag Race All Stars 7 Winners Cast on This Season's"));
    }

    public function testConstructor()
    {
        $uri = new Rfc3986Uri("https://username:password@example.com:8080/path?q=r#fragment");

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('username', $uri->getUser());
        $this->assertSame('password', $uri->getPassword());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('path', $uri->getPath());
        $this->assertSame('q=r', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
    }

    public function testIanaScheme()
    {
        $uri = Uri::fromRfc3986("chrome-extension://example.com");

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
        $uri = Uri::fromRfc3986("http://example.com/path/to/file2", "https://test.com");

        $this->assertSame('http', $uri->getScheme());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame('path/to/file2', $uri->getPath());
    }

    public function testWithBaseUriAndRelativeUrl()
    {
        $uri = Uri::fromRfc3986("/path/to/file2", "https://test.com");

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('test.com', $uri->getHost());
        $this->assertSame('path/to/file2', $uri->getPath());
    }

    public function testNormalization()
    {
        $uri = Uri::fromRfc3986("HttPs://0300.0250.0000.0001/path?query=foo%20bar");

        $this->assertSame('HttPs', $uri->getScheme());
        $this->assertSame('0300.0250.0000.0001', $uri->getHost());
        $this->assertSame('path', $uri->getPath());
        $this->assertSame('query=foo%20bar', $uri->getQuery());
    }

    public function testFileScheme()
    {
        $uri = Uri::fromRfc3986('file:///E:/Documents%20and%20Settings');

        $this->assertSame('file', $uri->getScheme());
        $this->assertNull($uri->getHost());
        $this->assertSame('E:/Documents%20and%20Settings', $uri->getPath());
    }

    public function testInstantiateWithoutConstructor()
    {
        $r = new \ReflectionClass(Rfc3986Uri::class);
        $uri = $r->newInstanceWithoutConstructor();

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Symfony\Polyfill\Php85\Uri\Rfc3986Uri object is not correctly initialized');

        $uri->getHost();
    }

    public function testToString()
    {
        $uri = Uri::fromRfc3986('http://example.com?foo=Hell%C3%B3+W%C3%B6rld');

        $this->assertSame('http://example.com?foo=Hell%C3%B3+W%C3%B6rld', $uri->__toString());
    }
}

<?php

namespace nadar\quill\tests;

use nadar\quill\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    public function safeUriProvider()
    {
        return [
            ['https://example.com/page?a=1&b=2'],
            ['http://example.com'],
            ['HTTPS://EXAMPLE.COM'],
            ['mailto:foo@bar.com'],
            ['tel:+41791234567'],
            ['#anchor'],
            ['/relative/path.html'],
            ['relative/path.html'],
            ['../up/and/over.html'],
            ['//protocol-relative.example.com/x'],
            ['?query=only'],
            [''],
        ];
    }

    public function unsafeUriProvider()
    {
        return [
            ['javascript:alert(1)'],
            ['JavaScript:alert(1)'],
            ['JaVaScRiPt:alert(document.domain)'],
            ["jav\tascript:alert(1)"],
            ["java\nscript:alert(1)"],
            ["java\rscript:alert(1)"],
            ["\x01javascript:alert(1)"],
            [' javascript:alert(1)'],
            ['vbscript:msgbox(1)'],
            ['VBScript:msgbox(1)'],
            ['data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=='],
            ['file:///etc/passwd'],
            ['ftp://example.com/file.txt'],
            ['custom:whatever'],
        ];
    }

    /**
     * @dataProvider safeUriProvider
     */
    public function testSafeUris($uri)
    {
        $this->assertTrue(Uri::isSafe($uri));
    }

    /**
     * @dataProvider unsafeUriProvider
     */
    public function testUnsafeUris($uri)
    {
        $this->assertFalse(Uri::isSafe($uri));
    }

    public function testSchemeCanBeAllowed()
    {
        $this->assertTrue(Uri::isSafe('javascript:alert(1)', ['javascript']));
        $this->assertFalse(Uri::isSafe('https://example.com', ['http']));
    }

    public function testNonStringValuesAreConsideredUnsafe()
    {
        // non strings have no scheme, but should not be treated as relative uris either
        // as they can not be validated at all.
        $this->assertFalse(Uri::isSafe(['javascript:alert(1)'], []));
    }

    public function testGetScheme()
    {
        $this->assertSame('https', Uri::getScheme('https://example.com'));
        $this->assertSame('https', Uri::getScheme('HtTpS://example.com'));
        $this->assertSame('javascript', Uri::getScheme("jav\tascript:alert(1)"));
        $this->assertFalse(Uri::getScheme('#anchor'));
        $this->assertFalse(Uri::getScheme('/path/to/file'));
        $this->assertFalse(Uri::getScheme('//example.com'));
        $this->assertFalse(Uri::getScheme(null));
        $this->assertFalse(Uri::getScheme([]));
    }

    public function testGetDataMediaType()
    {
        $this->assertSame('image/png', Uri::getDataMediaType('data:image/png;base64,iVBORw0KGgo='));
        $this->assertSame('image/svg+xml', Uri::getDataMediaType('DATA:image/SVG+XML;charset=utf-8,<svg/>'));
        $this->assertSame('text/html', Uri::getDataMediaType('data:text/html,<script>alert(1)</script>'));
        $this->assertFalse(Uri::getDataMediaType('data:,helloworld'));
        $this->assertFalse(Uri::getDataMediaType('https://example.com/cat.png'));
        $this->assertFalse(Uri::getDataMediaType(null));
    }
}

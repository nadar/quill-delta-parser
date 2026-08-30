<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\listener\Image;
use nadar\quill\listener\Link;
use PHPUnit\Framework\TestCase;

class UriSchemeTest extends TestCase
{
    public function unsafeLinkProvider()
    {
        return [
            ['[{"attributes":{"link":"javascript:alert(document.domain)"},"insert":"clickme"},{"insert":"\\n"}]'],
            ['[{"attributes":{"link":"JaVaScRiPt:alert(document.domain)"},"insert":"clickme"},{"insert":"\\n"}]'],
            ["[{\"attributes\":{\"link\":\"jav\\u0009ascript:alert(1)\"},\"insert\":\"clickme\"},{\"insert\":\"\\n\"}]"],
            ["[{\"attributes\":{\"link\":\"jav\\u000aascript:alert(1)\"},\"insert\":\"clickme\"},{\"insert\":\"\\n\"}]"],
            ["[{\"attributes\":{\"link\":\"\\u0001javascript:alert(1)\"},\"insert\":\"clickme\"},{\"insert\":\"\\n\"}]"],
            ['[{"attributes":{"link":"vbscript:msgbox(1)"},"insert":"clickme"},{"insert":"\\n"}]'],
            ['[{"attributes":{"link":"data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=="},"insert":"clickme"},{"insert":"\\n"}]'],
        ];
    }

    /**
     * @dataProvider unsafeLinkProvider
     */
    public function testUnsafeLinkIsNeutralized($json)
    {
        $output = (new Lexer($json))->render();

        $this->assertStringNotContainsString('javascript:', strtolower($output));
        $this->assertStringNotContainsString('vbscript:', strtolower($output));
        $this->assertStringNotContainsString('data:', strtolower($output));
        $this->assertSame('<p><a href="#" target="_blank">clickme</a></p>', trim(str_replace(PHP_EOL, '', $output)));
    }

    public function testUnsafeMultilineLinkIsNeutralized()
    {
        // a link spanning multiple lines, as produced by quill when several paragraphs
        // share the same link.
        $json = '[{"attributes":{"link":"javascript:alert(1)"},"insert":"first"},{"insert":"\\n","attributes":{"link":"javascript:alert(1)"}}'
              . ',{"attributes":{"link":"javascript:alert(1)"},"insert":"second"},{"insert":"\\n","attributes":{"link":"javascript:alert(1)"}},{"insert":"\\n"}]';
        $output = (new Lexer($json))->render();

        $this->assertStringNotContainsString('javascript:', $output);
        $this->assertSame('<p><a href="#" target="_blank">firstsecond</a></p>', trim(str_replace(PHP_EOL, '', $output)));
    }

    public function testSafeLinksAreStillRendered()
    {
        $cases = [
            '[{"attributes":{"link":"https://example.com"},"insert":"a"},{"insert":"\\n"}]' => '<p><a href="https://example.com" target="_blank">a</a></p>',
            '[{"attributes":{"link":"mailto:foo@bar.com"},"insert":"b"},{"insert":"\\n"}]' => '<p><a href="mailto:foo@bar.com" target="_blank">b</a></p>',
            '[{"attributes":{"link":"#section"},"insert":"c"},{"insert":"\\n"}]' => '<p><a href="#section" target="_blank">c</a></p>',
            '[{"attributes":{"link":"/relative/page.html"},"insert":"d"},{"insert":"\\n"}]' => '<p><a href="/relative/page.html" target="_blank">d</a></p>',
            '[{"attributes":{"link":"//cdn.example.com/x"},"insert":"e"},{"insert":"\\n"}]' => '<p><a href="//cdn.example.com/x" target="_blank">e</a></p>',
        ];

        foreach ($cases as $json => $expected) {
            $this->assertSame($expected, trim(str_replace(PHP_EOL, '', (new Lexer($json))->render())));
        }
    }

    public function testUnsafeImageIsNotRendered()
    {
        $output = (new Lexer('[{"insert":{"image":"javascript:alert(1)"}},{"insert":"text\\n"}]'))->render();

        $this->assertStringNotContainsString('<img', $output);
        $this->assertStringNotContainsString('javascript:', $output);
        $this->assertSame('<p>text</p>', trim(str_replace(PHP_EOL, '', $output)));
    }

    public function testDataImageWithHtmlMediaTypeIsBlocked()
    {
        $output = (new Lexer('[{"insert":{"image":"data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=="}},{"insert":"text\\n"}]'))->render();

        $this->assertStringNotContainsString('<img', $output);
        $this->assertStringNotContainsString('data:text/html', $output);
    }

    public function testSafeImagesAreStillRendered()
    {
        $output = (new Lexer('[{"insert":{"image":"https://example.com/cat.png"}},{"insert":"\\n"}]'))->render();
        $this->assertSame('<p><img src="https://example.com/cat.png" alt="" class="img-responsive img-fluid" /></p>', trim(str_replace(PHP_EOL, '', $output)));

        // base64 images are a common quill paste scenario and stay enabled by default,
        // but restricted to the configured media types.
        $output = (new Lexer('[{"insert":{"image":"data:image/png;base64,iVBORw0KGgo="}},{"insert":"\\n"}]'))->render();
        $this->assertStringContainsString('src="data:image/png;base64,iVBORw0KGgo="', $output);
    }

    public function testUnsafeVideoIsNotRendered()
    {
        $output = (new Lexer('[{"insert":{"video":"javascript:alert(1)"}},{"insert":"\\n"}]'))->render();

        $this->assertStringNotContainsString('<iframe', $output);
        $this->assertStringNotContainsString('javascript:', $output);
    }

    public function testSafeVideoIsStillRendered()
    {
        $output = (new Lexer('[{"insert":{"video":"https://www.youtube.com/embed/dQw4w9WgXcQ"}},{"insert":"\\n"}]'))->render();

        $this->assertStringContainsString('src="https://www.youtube.com/embed/dQw4w9WgXcQ"', $output);
    }

    public function testSchemeAllowlistCanBeAdjusted()
    {
        // restrict links to https only - mailto would be neutralized as well
        $link = new Link();
        $link->safeSchemes = ['https'];

        $lexer = new Lexer('[{"attributes":{"link":"mailto:foo@bar.com"},"insert":"x"},{"insert":"\\n"}]');
        $lexer->overwriteListener(new Link(), $link);
        $output = $lexer->render();

        $this->assertStringNotContainsString('mailto:', $output);
        $this->assertStringContainsString('href="#"', $output);
    }

    public function testDataMediaTypesCanBeRestrictedFurther()
    {
        $image = new Image();
        // disallow data uris entirely
        $image->safeSchemes = ['http', 'https'];

        $lexer = new Lexer('[{"insert":{"image":"data:image/png;base64,iVBORw0KGgo="}},{"insert":"text\\n"}]');
        $lexer->overwriteListener(new Image(), $image);
        $output = $lexer->render();

        $this->assertStringNotContainsString('<img', $output);
        $this->assertSame('<p>text</p>', trim(str_replace(PHP_EOL, '', $output)));
    }
}

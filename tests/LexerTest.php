<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use PHPUnit\Framework\TestCase;
use nadar\quill\listener\Image;

class LexerTest extends TestCase
{
    public function testIsJson()
    {
        $this->assertTrue(Lexer::isJson('{}'));
        $this->assertFalse(Lexer::isJson([]));
    }

    public function testOverridePlugin()
    {
        $imageListener = new Image();
        $imageListener->wrapper = '<img src="{src}" bar="foo" />';

        $lexer = new Lexer('[{"insert": {"image": "https://example.com/image.jpg"}},{"insert": "text\n"}]');
        $lexer->registerListener($imageListener);

        $this->assertSame('<p><img src="https://example.com/image.jpg" bar="foo" />text</p>', $lexer->render());
    }
}

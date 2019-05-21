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

    public function testLinesAndNewlines()
    {
        $lexer = new Lexer('{"ops":[{"insert":"Test\n"},{"attributes":{"bold":true},"insert":"test"},{"attributes":{"list":"ordered"},"insert":"\n"}]}');
        $lexer->render();
        $lines = $lexer->getLines();

        $this->assertTrue($lines[0]->hasNewline());
        $this->assertTrue($lines[0]->hasEndNewline());
        
    }

    public function testLineHasNewLineStatus()
    {
        $lexer = new Lexer('{"ops":[{"insert":"text\nopen"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"close"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"after\n"}]}');
        $lexer->render();
        $lexer->debug = true;
        $lines = $lexer->getLines();

        $this->assertTrue($lines[0]->hasNewline());
        $this->assertFalse($lines[0]->hasEndNewline());

        $this->assertFalse($lines[1]->hasNewline());
        $this->assertFalse($lines[1]->hasEndNewline());

        $this->assertTrue($lines[2]->hasNewline());
        $this->assertTrue($lines[2]->hasEndNewline());

        $this->assertFalse($lines[3]->hasNewline());
        $this->assertFalse($lines[3]->hasEndNewline());

        $this->assertTrue($lines[4]->hasNewline());
        $this->assertTrue($lines[4]->hasEndNewline());

        $this->assertTrue($lines[5]->hasNewline());
        $this->assertTrue($lines[5]->hasEndNewline());
    }
}

<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    public function testIsJson()
    {
        $this->assertTrue(Lexer::isJson('{}'));
        $this->assertFalse(Lexer::isJson([]));
    }
}

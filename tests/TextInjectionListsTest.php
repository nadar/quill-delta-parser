<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use PHPUnit\Framework\TestCase;

class TextInjectionListsTest extends TestCase
{
    public function testUnknownTypeException()
    {
        $lexer = new Lexer('[{"insert": "1"},{"attributes": {"list": "<script>alert(1)</script>"},"insert": "\n"},{"insert": "2"},{"attributes": {"list": "<script>alert(1)</script>"},"insert": "\n"}]');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unknown list type');
        $lexer->render();
    }
}

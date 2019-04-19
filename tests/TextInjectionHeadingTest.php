<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use PHPUnit\Framework\TestCase;

class TextInjectionHeadingTest extends TestCase
{
    public function testUnknownLevelException()
    {
        $lexer = new Lexer('[{"insert": "heading"}, {"insert": "\n", "attributes": {"header": "<script>alert(1)</script>"}}]');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unknown heading level');
        $lexer->render();
    }
}

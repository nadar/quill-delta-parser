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
        $this->expectExceptionMessage('An unknown heading level "<script>alert(1)</script>" has been detected.');
        $lexer->render();
    }
}

<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use PHPUnit\Framework\TestCase;

class AlignInjectionTest extends TestCase
{
    public function testUnknownAlignException()
    {
        $lexer = new Lexer('[{"insert":"Test"},{"attributes":{"align":"<script>alert(1)</script>"},"insert":"\n"}]');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('An unknown alignment "<script>alert(1)</script>" has been detected.');
        $lexer->render();
    }
}

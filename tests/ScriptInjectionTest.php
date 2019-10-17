<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use PHPUnit\Framework\TestCase;

class ScriptInjectionTest extends TestCase
{
    public function testUnknownScriptException()
    {
        $lexer = new Lexer('[{"attributes":{"script":"<script>alert(1)</script>"}, "insert":"Testing\n"}]');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('An unknown script tag "<script>alert(1)</script>" has been detected.');
        $lexer->render();
    }
}

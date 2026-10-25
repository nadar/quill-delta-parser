<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\Line;
use nadar\quill\listener\Bold;
use nadar\quill\Pick;
use PHPUnit\Framework\TestCase;

class PickTest extends TestCase
{
    public function testDeprecatedMagicGetter()
    {
        $lexer = new Lexer(['insert' => 'foo']);
        $line = new Line(0, 'input', [], $lexer, false, false);
        $pick = new Pick(new Bold(), $line, ['foo' => 'bar'], 0);

        $notices = [];
        set_error_handler(function ($errno, $errstr) use (&$notices) {
            $notices[] = $errstr;
            return true;
        }, E_USER_NOTICE);

        try {
            $this->assertSame('bar', $pick->foo);
            $this->assertNull($pick->unknown);
        } finally {
            restore_error_handler();
        }

        $this->assertCount(2, $notices);
        $this->assertStringContainsString("use `optionValue('foo')` instead", $notices[0]);
        $this->assertSame('bar', $pick->optionValue('foo'));
    }
}

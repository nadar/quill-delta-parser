<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\Line;
use PHPUnit\Framework\TestCase;


class LineTest extends TestCase
{
    public function testsNextLine()
    {
        $lexer = new Lexer(['insert' => 'foo']);

        $line = new Line(0, 'input', [], $lexer, false, false);
        $this->assertFalse($line->next());
        $this->assertFalse($line->next(function(Line $line) {
            return true;
        }));
        $this->assertFalse($line->next(function(Line $line) {
            return false;
        }));

    }
}
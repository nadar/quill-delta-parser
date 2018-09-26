<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\Line;
use PHPUnit\Framework\TestCase;
use nadar\quill\Listener;
use nadar\quill\Pick;

class ListenerTest extends TestCase
{
    public function testPicks()
    {
        $lexer = new Lexer(['insert' => 'foo']);
        $line = new Line(0, 'input', [], $lexer, false, false);
        $listener = new TestListener();
        $listener->pick($line);

        foreach ($listener->picks() as $pick) {
            $this->assertTrue($pick->isFirst());
            $this->assertTrue($pick->isLast());
        }

        $this->assertNull($listener->render($lexer));
    }
}

class TestListener extends Listener
{
    public function type(): int
    {
        self::TYPE_BLOCK;
    }

    public function process(\nadar\quill\Line $line)
    {
    }
}

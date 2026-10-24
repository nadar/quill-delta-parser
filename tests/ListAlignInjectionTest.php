<?php

namespace nadar\quill\tests;

use Exception;
use nadar\quill\Lexer;
use PHPUnit\Framework\TestCase;

class ListAlignInjectionTest extends TestCase
{
    public function testUnknownListItemAlignmentThrowsException()
    {
        $lexer = new Lexer([
            ['insert' => 'Item'],
            ['insert' => "\n", 'attributes' => ['list' => 'bullet', 'align' => 'center;"><script>alert(1)</script>']],
        ]);

        $this->expectException(Exception::class);
        $lexer->render();
    }
}

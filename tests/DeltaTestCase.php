<?php

declare(strict_types=1);

namespace nadar\quill\tests;

error_reporting(E_ALL);

use PHPUnit\Framework\TestCase;
use nadar\quill\Lexer;

class DeltaTestCase extends TestCase
{
    public $json;
    public $html;

    public function getLexer()
    {
        return new Lexer($this->json);
    }

    public function listeners(Lexer $lexer)
    {
    }

    public function testOutput()
    {
        $parser = $this->getLexer();
        $this->listeners($parser);
        $this->assertSame(trim($this->html), trim($parser->render()));
    }
}

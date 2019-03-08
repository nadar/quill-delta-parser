<?php

namespace nadar\quill\tests;

use PHPUnit\Framework\TestCase;
use nadar\quill\Lexer;
use nadar\quill\listener\Text;
use nadar\quill\listener\Heading;
use nadar\quill\listener\Bold;

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
        $this->assertSame(trim(str_replace(PHP_EOL, '', $this->html)), trim($parser->render()));
    }
}

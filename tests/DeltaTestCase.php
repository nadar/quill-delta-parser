<?php

namespace nadar\quill\tests;

use PHPUnit\Framework\TestCase;
use nadar\quill\Lexer;
use nadar\quill\listener\Text;
use nadar\quill\listener\Heading;
use nadar\quill\listener\Bold;

/**
 * @see https://www.transmute-coffee.com/php-quill-renderer.php#demo
 */
class DeltaTestCase extends TestCase
{
    public $json;
    public $html;

    public function testOutput()
    {
        $parser = new Lexer($this->json);
        $parser->initBuiltInListeners();

        $this->assertSame(trim(str_replace(PHP_EOL, '', $this->html)), trim($parser->render()));
    }
}

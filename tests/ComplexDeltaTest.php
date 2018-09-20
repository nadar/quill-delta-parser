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
class ComplexDeltaTest extends TestCase
{
    public function testOutput()
    {
        $json = <<<'JSON'
{"ops":[
    {
      "insert": "Heading!"
    },
    {
      "attributes": {
        "header": 1
      },
      "insert": "\n"
    },
    {
      "insert": "\n"
    },
    {
      "insert": "Some text! Bold! "
    },
    {
      "insert": "\n\n"
    },
    {
      "insert": "Heading2!"
    },
    {
      "attributes": {
        "header": 2
      },
      "insert": "\n"
    },
    {
      "insert": "\n"
    },
    {
      "insert": "We need bullets:"
    },
    {
      "insert": "\n\n"
    },
    {
      "insert": "BU"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "LET"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "\n"
    },
    {
      "insert": "And more!"
    },
    {
      "insert": "\n\n"
    },
    {
      "insert": "LET"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "BU"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    }
  ]
}
JSON;

        $same = <<<'EOT'
<h1>Heading!</h1>
<p></p><p>
Some text! Bold! </p>
<h2>Heading2!</h2>
<p>We need bullets:</p>
<ul>
<li>BU</li>
<li>LET</li>
</ul>
<p>And more!</p>
<ul>
<li>LET</li>
<li>BU</li>
</ul>
EOT;
        
        $parser = new Lexer($json);
        $parser->initBuiltInListeners();

        $this->assertSame(trim(str_replace(PHP_EOL, '', $same)), trim($parser->render()));
    }

}
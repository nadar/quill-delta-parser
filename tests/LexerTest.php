<?php

namespace nadar\quill\tests;

use PHPUnit\Framework\TestCase;
use nadar\quill\Lexer;


class LexerTest extends TestCase
{
    

    public function testOutput()
    {
        $json = <<<'JSON'
{"ops":[
    {
      "insert": "text bfore heading\nHeading!"
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
      "insert": "Some text! "
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
]}
JSON;

        $lexer = new Lexer($json);


        $html = '<h1>Heading</h1>
        <p><br></p><p>Some text!</p>
        <p><br></p>
        <h2>Heading2</h2>';

        $this->assertSame($html, $lexer->render());

    }
}
<?php

namespace nadar\quill\tests;

use PHPUnit\Framework\TestCase;
use nadar\quill\Parser;
use nadar\quill\listener\Text;
use nadar\quill\listener\Heading;
use nadar\quill\listener\Bold;


class ParserTest extends TestCase
{
    public function testJsonToArray()
    {
        $parser = new Parser('{
            "ops": [
              {
                "insert": "Title"
              },
              {
                "attributes": {
                  "header": 1
                },
                "insert": "\n"
              },
              {
                "insert": "Text with "
              },
              {
                "attributes": {
                  "bold": true
                },
                "insert": "bold"
              },
              {
                "insert": " element.\n"
              }
            ]
          }');

          $parser->registerListener(new Heading());
          $parser->registerListener(new Text());
          $parser->registerListener(new Bold());

          $this->assertTrue(is_array($parser->getJsonArray()));

          var_dump($parser->render());
          $this->assertSame('<h1>Title</h1>'.PHP_EOL.'<p>Text with <strong>bold</strong> element</p>', $parser->render());
    }
}
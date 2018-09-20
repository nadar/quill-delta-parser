<?php

namespace nadar\quill\tests;

use PHPUnit\Framework\TestCase;
use nadar\quill\Lexer;
use nadar\quill\listener\Text;
use nadar\quill\listener\Heading;
use nadar\quill\listener\Bold;


class ParserTest extends TestCase
{
    public $asserts = [
      
      '<ul><li>Foo</li><li>Bar</li></ul><p><br></p>' => '[
      {
        "insert": "Foo"
      },
      {
        "attributes": {
          "list": "bullet"
        },
        "insert": "\n"
      },
      {
        "insert": "Bar"
      },
      {
        "attributes": {
          "list": "bullet"
        },
        "insert": "\n"
      },
      {
        "insert": "\n"
      }]',
      
      
      '<p>Hallo</p><p>Wie</p><p>Gehts?</p>' => '{"ops": [{"insert": "Hallo\nWie\nGehts?\n"}]}',
      '<p>Hallo</p><p>Wie</p><p><br></p><p>Shift</p><p>Enter</p>' => '[{"insert": "Hallo\nWie\n\nShift\nEnter\n"}]',
      '<h1>Title</h1><p>Text with <strong>bold</strong> element.</p>' => '{
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
      }',
      
    ];

    public function testJsonToArray()
    {
      foreach ($this->asserts as $e => $j) {
        
          $parser = new Lexer($j);
          $parser->initBuiltInListeners();

          $this->assertTrue(is_array($parser->getJsonArray()));

          $this->assertSame($e, $parser->render());

          unset($parser);
      }
    }

}
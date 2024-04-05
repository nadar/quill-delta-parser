<?php

namespace nadar\quill\tests;

class Issue87Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{
    "ops": 
    [
      {
        "insert": "This is begin text:\n\n"
      },
      {
        "attributes": {
          "bold": true
        },
        "insert": "Bold text"
      },
      {
        "insert": " and regular text"
      },
      {
        "attributes": {
          "list": "bullet"
        },
        "insert": "\n"
      },
      {
        "insert": "Another bullet"
      },
      {
        "attributes": {
          "list": "bullet"
        },
        "insert": "\n"
      },
      {
        "insert": "Another text after list"
      }
    ]
}
JSON;

    public $html = <<<'EOT'
    <p>This is begin text:</p><p><br></p><ul><li><strong>Bold text</strong> and regular text</li><li>Another bullet</li></ul><p>Another text after list</p>
EOT;
}
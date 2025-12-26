<?php

namespace nadar\quill\tests;

use nadar\quill\listener\Size;

class SizeAttributeIgnoreTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    {
      "attributes": {
        "size": "21pt"
      },
      "insert": "Large text"
    },
    {
      "insert": " normal "
    },
    {
      "attributes": {
        "size": "10px"
      },
      "insert": "small"
    },
    {
      "insert": ".\n"
    }
  ]
}
JSON;

    public $html = '<p>Large text normal small.</p>';

    public function listeners(\nadar\quill\Lexer $lexer)
    {
        $size = new Size();
        $size->ignore = true;
        $lexer->registerListener($size);
    }
}

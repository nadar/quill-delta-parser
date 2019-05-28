<?php

namespace nadar\quill\tests;

use nadar\quill\listener\Color;

class TextAttributesNoColorTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {
      "attributes": {
        "color": "#27333a",
        "bold": true
      },
      "insert": "Was: "
    },
    {
      "attributes": {
        "color": "#27333a"
      },
      "insert": "Mi"
    },
    {
      "insert": "\n"
    },
    {
      "attributes": {
        "color": "#27333a",
        "bold": true
      },
      "insert": "Wann:"
    },
    {
      "attributes": {
        "color": "#27333a"
      },
      "insert": " März"
    },
    {
      "insert": "\n"
    },
    {
      "attributes": {
        "color": "#27333a",
        "bold": true
      },
      "insert": "Wo:"
    },
    {
      "attributes": {
        "color": "#27333a"
      },
      "insert": " Aarau"
    },
    {
        "insert": "\n"
    }]
JSON;

    public $html = <<<'EOT'
    <p><strong>Was: </strong>Mi</p><p><strong>Wann:</strong> März</p><p><strong>Wo:</strong> Aarau</p>
EOT;

    public function listeners(\nadar\quill\Lexer $lexer)
    {
        $color = new Color();
        $color->ignore = true;

        $lexer->registerListener($color);
    }
}

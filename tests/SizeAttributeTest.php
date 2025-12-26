<?php

namespace nadar\quill\tests;

use nadar\quill\listener\Size;

class SizeAttributeTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    {
      "attributes": {
        "color": "#e74c3c",
        "size": "21pt",
        "font": "Lancelot, cursive"
      },
      "insert": "Lorem ipsum!"
    },
    {
      "insert": "\n"
    },
    {
      "attributes": {
        "size": "14px"
      },
      "insert": "Normal size text"
    },
    {
      "insert": " and "
    },
    {
      "attributes": {
        "size": "24pt"
      },
      "insert": "large size"
    },
    {
      "insert": ".\n"
    },
    {
      "attributes": {
        "size": "1.5em"
      },
      "insert": "Relative size"
    },
    {
      "insert": "\n"
    }
  ]
}
JSON;

    public $html = <<<'EOT'
<p><span style="font-size:21pt"><span style="font-family: Lancelot, cursive;"><span style="color:#e74c3c">Lorem ipsum!</span></span></span></p><p><span style="font-size:14px">Normal size text</span> and <span style="font-size:24pt">large size</span>.</p><p><span style="font-size:1.5em">Relative size</span></p>
EOT;
}

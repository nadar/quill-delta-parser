<?php

namespace nadar\quill\tests;

class AttributeTextTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    {
      "attributes": {
        "color": "#000000"
      },
      "insert": "xyz"
    },
    {
      "insert": "\n"
    },
    {
      "attributes": {
        "color": "#fff"
      },
      "insert": "xyz"
    },
    {
      "insert": "\n"
    }
  ]
}
JSON;

    public $html = <<<'EOT'
<p style="color:#000000">xyz</p><p><br></p><p style="color:#fff">xyz</p><p><br></p>
EOT;
}

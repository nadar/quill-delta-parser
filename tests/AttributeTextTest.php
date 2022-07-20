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
<p><span style="color:#000000">xyz</span></p>
<p><span style="color:#fff">xyz</span></p>
EOT;
}

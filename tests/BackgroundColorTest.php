<?php
namespace nadar\quill\tests;

class BackgroundColorTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    {
      "attributes": {
        "background": "#000000"
      },
      "insert": "xyz"
    },
    {
      "insert": "\n"
    },
    {
      "attributes": {
        "background": "#fff"
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
<p><span style="background-color:#000000">xyz</span></p><p><span style="background-color:#fff">xyz</span></p>
EOT;
}

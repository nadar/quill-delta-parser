<?php
namespace nadar\quill\tests;

class Issue70Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    {
      "attributes": {
        "height": "141",
        "width": "210"
      },
      "insert": {
        "image": "https://localhost/test.jpg"
      }
    },
    {
      "insert": "\n"
    }
  ]
}
JSON;

    public $html = <<<'EOT'
    <p><img src="https://localhost/test.jpg" width="210" height="141" alt="" class="img-responsive img-fluid" /></p>
EOT;
}

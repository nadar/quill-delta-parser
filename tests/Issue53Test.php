<?php
namespace nadar\quill\tests;

class Issue53Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[{
    "attributes": {
      "list": {
        "depth": 0,
        "type": "bullet"
      }
    },
    "insert": "\n"
  },
  {
    "insert": "Bullet point content"
  }
]}
JSON;

    public $html = <<<'EOT'
    <ul><li>Bullet point content</li></ul>
EOT;
}

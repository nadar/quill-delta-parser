<?php
namespace nadar\quill\tests;

class Issue53TestFixedTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[
  {
    "insert": "Bullet point content"
  },
  {
    "attributes": {
      "list": {
        "depth": 0,
        "type": "bullet"
      }
    },
    "insert": "\n"
  }
]}
JSON;

    public $html = <<<'EOT'
    <ul><li>Bullet point content</li></ul>
EOT;
}

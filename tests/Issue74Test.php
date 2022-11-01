<?php
namespace nadar\quill\tests;

class Issue74Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    {
      "insert": "Level 1A"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "Level 2A"
    },
    {
      "attributes": {
        "indent": 1,
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "Level 2B"
    },
    {
      "attributes": {
        "indent": 1,
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "Level 1B"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    }
  ]
}
JSON;

    public $html = <<<'EOT'
    <ul><li>Level 1A<ul><li>Level 2A</li><li>Level 2B</li></ul></li><li>Level 1B</li></ul>
EOT;
}

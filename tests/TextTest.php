<?php

namespace nadar\quill\tests;

class TextTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    {
      "insert": "normal text"
    }
  ]
}
JSON;

    public $html = <<<'EOT'
<p>normal text</p>
EOT;
}

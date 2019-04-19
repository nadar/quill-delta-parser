<?php

namespace nadar\quill\tests;

use PHPUnit\Framework\TestCase;

class TextInjectionNestedTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[
  {
  	"insert": "<script>alert(1)</script>",
  	"attributes": {
  	  "bold": true,
  	  "italic": true
  	}
  },
  {
    "insert": "\n"
  }
]}
JSON;

    public $html = <<<'EOT'
<p><em><strong>&lt;script&gt;alert(1)&lt;/script&gt;</strong></em></p>
EOT;
}

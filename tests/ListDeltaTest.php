<?php

namespace nadar\quill\tests;

class ListDeltaTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {
      "attributes": {
        "bold": true
      },
      "insert": "Infobox"
    },
    {
      "insert": "\n1"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "2"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "3"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "\n\nFooter\n"
    }
]
JSON;

public $html = <<<'EOT'
<p><strong>Infobox</strong></p>
<ul>
<li>1</li>
<li>2</li>
<li>3</li>
</ul>
<p><br></p>
<p><br></p>
<p>Footer</p>
EOT;
}
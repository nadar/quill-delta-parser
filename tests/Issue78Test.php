<?php

namespace nadar\quill\tests;

class Issue78Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{
    "ops": 
    [
        {
            "insert": "JUST A LIST",
            "attributes": {
                "bold": true,
                "color": "#000000",
                "background": "transparent"
            }
        },
        {
            "insert": "\n",
            "attributes": {
                "align": "center"
            }
        },
        {
            "insert": "\n\n",
            "attributes": {
                "list": "ordered"
            }
        },
        {
            "insert": "\n"
        },
        {
            "insert": "New title",
            "attributes": {
                "bold": true,
                "color": "#000000",
                "background": "transparent"
            }
        },
        {
            "insert": "\n\n"
        }
    ]
}
JSON;

    public $html = <<<'EOT'
    <p style="text-align: center;"><span style="color:#000000"><strong>JUST A LIST</strong></span></p>
<ol>
   <li></li>
   <li></li>
</ol>
<p style="text-align: center;"><span style="color:#000000"><strong>New title</strong></span></p>
EOT;
}
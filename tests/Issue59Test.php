<?php

namespace nadar\quill\tests;

class Issue59Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{
   "ops":[
      {
        "attributes":{
            "link":"https://example.com"
        },
        "insert":"Hello "
      },
      {
        "attributes":{
            "link":"https://example.com",
            "italic":true
        },
        "insert":"world"
      },
      {
        "insert":"\n"
      }
   ]
}
JSON;

    public $html = <<<'EOT'
<p><a href="https://example.com" target="_blank">Hello <em>world</em></a></p>
EOT;
}

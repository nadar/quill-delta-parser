<?php

namespace nadar\quill\tests;

class EndOfNewlineTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[
  {"insert":"\n\nFooter.\n"}    
]}
JSON;
    public $html = <<<'EOT'
<p><br></p>
<p><br></p>
<p>Footer.</p>
EOT;
}

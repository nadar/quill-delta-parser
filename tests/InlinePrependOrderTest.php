<?php
namespace nadar\quill\tests;

class InlinePrependOrderTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[{"attributes":{"italic":true},"insert":"italic"},{"attributes":{"bold":true},"insert":"bold"},{"insert":"\n"}]}
JSON;

    public $html = <<<'EOT'
<p><em>italic</em><strong>bold</strong></p>
EOT;
}

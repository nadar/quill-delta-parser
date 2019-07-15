<?php
namespace nadar\quill\tests;

class IgnoreUnregisteredListenersTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[{"insert":"1"},{"attributes":{"unknownAttribute":{"id": 999}},"insert":"2"},{"insert":"3\n"}]}
JSON;

    public $html = <<<'EOT'
<p>123</p>
EOT;
}

<?php
namespace nadar\quill\tests;

class PrependInlineIndexTest extends DeltaTestCase
{
    public $json = <<<'JSON'
    {"ops":[
        {"attributes":{"italic":true},"insert":"italic"},
        {"attributes":{"bold":true,"link":"https://nadar.io"},"insert":"link"},
        {"insert":"\n"}
    ]}
JSON;

    public $html = <<<'EOT'
<p>
<em>italic</em>
<a href="https://nadar.io" target="_blank"><strong>link</strong></a>
</p>
EOT;
}
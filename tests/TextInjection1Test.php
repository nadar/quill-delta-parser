<?php

namespace nadar\quill\tests;

class TextInjection1Test extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {"insert":"normal text"},
    {"insert":"<script type='text/javascript'>alert(\"html injection\");</script>"},
    {"insert":"bold text", "attributes":{"bold":true}},
    {"insert":"\n"}
]
JSON;

    public $html = <<<'EOT'
<p>normal text</p><p>&lt;script type=&apos;text/javascript&apos;&gt;alert(&quot;html injection&quot;);&lt;/script&gt;<strong>bold text</strong></p>
EOT;
}

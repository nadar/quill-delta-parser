<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\listener\Text;

class TextInjectionFlagsTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {"insert": "normal text"},
    {"insert": "<script type='text/javascript'>alert(\"html injection\");</script>"},
    {"insert": "bold text", "attributes": {"bold": true}},
    {"insert": "\n"}
]
JSON;

    public $html = <<<'EOT'
<p>normal text</p><p>&lt;script type='text/javascript'&gt;alert(&quot;html injection&quot;);&lt;/script&gt;<strong>bold text</strong></p>
EOT;

    public function listeners(Lexer $lexer)
    {
        Text::$escapeFlags = ENT_COMPAT;
    }
}

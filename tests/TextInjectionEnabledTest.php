<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\listener\Text;

class TextInjectionEnabledTest extends DeltaTestCase
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
<p>normal text</p><p><script type='text/javascript'>alert("html injection");</script><strong>bold text</strong></p>
EOT;

    public function getLexer()
    {
        $lexer = new Lexer($this->json);
        $lexer->escapeInput = false;

        return $lexer;
    }

    public function listeners(Lexer $lexer)
    {
        Text::$escapeFlags = ENT_COMPAT;
    }
}

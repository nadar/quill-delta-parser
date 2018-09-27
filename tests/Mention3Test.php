<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\listener\Mention;

class Mention3Test extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {"insert":{"mention":{"id":"1","value":"at mention","denotationChar":"@"}}},
    {"insert":" between "},
    {"insert":{"mention":{"id":"16","value":"hash mention","denotationChar":"#"}}},
    {"insert":"\n"}
]
JSON;
    public $html = <<<'EOT'
<p>at mention between hash mention</p>
EOT;

    public function getLexer()
    {
        $lexer = new Lexer($this->json);
        $lexer->registerListener(new Mention);

        return $lexer;
    }
}

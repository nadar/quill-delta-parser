<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\listener\Mention;

/**
 * Inline attributes on an embed must not prevent the embed listener from converting it.
 *
 * @see https://github.com/nadar/quill-delta-parser/issues/92
 */
class Issue92Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops": [
    {"insert": "test ", "attributes": {"bold": true}},
    {"insert": {"mention": {"id": "1", "value": "Basil", "denotationChar": "@"}}, "attributes": {"bold": true}},
    {"insert": " test", "attributes": {"bold": true}},
    {"insert": " and "},
    {"insert": {"mention": {"id": "2", "value": "John", "denotationChar": "@"}}, "attributes": {"italic": true, "link": "https://example.com"}},
    {"insert": "\n"}
]}
JSON;

    public $html = <<<'EOT'
<p><strong>test </strong><strong>Basil</strong><strong> test</strong> and <a href="https://example.com" target="_blank"><em>John</em></a></p>
EOT;

    public function listeners(Lexer $lexer)
    {
        $lexer->registerListener(new Mention());
    }
}

<?php

namespace nadar\quill\tests;

/**
 * Consecutive blockquote lines are rendered into one blockquote.
 *
 * @see https://github.com/nadar/quill-delta-parser/issues/93
 */
class BlockquoteMultilineTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops": [
    {"insert": "Just text\n"},
    {"attributes": {"bold": true}, "insert": "Quo"},
    {"insert": "te"},
    {"attributes": {"blockquote": true}, "insert": "\n"},
    {"insert": "Quote2"},
    {"attributes": {"blockquote": true}, "insert": "\n"},
    {"insert": "Between\n"},
    {"insert": "Quote3"},
    {"attributes": {"blockquote": true}, "insert": "\n"}
]}
JSON;

    public $html = <<<'EOT'
<p>Just text</p>
<blockquote><strong>Quo</strong>te<br>Quote2</blockquote>
<p>Between</p>
<blockquote>Quote3</blockquote>
EOT;
}

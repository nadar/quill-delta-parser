<?php

namespace nadar\quill\tests;

/**
 * Consecutive code block lines are rendered into one code block.
 *
 * @see https://github.com/nadar/quill-delta-parser/issues/93
 */
class CodeBlockMultilineTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops": [
    {"insert": "Just text\n"},
    {"insert": "if (a < b) {"},
    {"attributes": {"code-block": true}, "insert": "\n"},
    {"insert": "    return;"},
    {"attributes": {"code-block": true}, "insert": "\n"},
    {"attributes": {"code-block": true}, "insert": "\n"},
    {"insert": "}"},
    {"attributes": {"code-block": true}, "insert": "\n"},
    {"insert": "Between\n"},
    {"insert": "single"},
    {"attributes": {"code-block": true}, "insert": "\n"}
]}
JSON;

    public $html = <<<'EOT'
<p>Just text</p>
<pre><code>if (a &lt; b) {
    return;

}</code></pre>
<p>Between</p>
<pre><code>single</code></pre>
EOT;
}

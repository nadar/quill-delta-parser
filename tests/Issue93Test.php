<?php

namespace nadar\quill\tests;

/**
 * The exact delta from the issue: consecutive blockquote and code block lines are combined into one
 * block each, also when a code block is directly followed by a blockquote.
 *
 * @see https://github.com/nadar/quill-delta-parser/issues/93
 */
class Issue93Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops": [
    {"insert": "Just text\n"},
    {"attributes": {"bold": true}, "insert": "Quo"},
    {"insert": "te"},
    {"attributes": {"blockquote": true}, "insert": "\n"},
    {"insert": "Quote2"},
    {"attributes": {"blockquote": true}, "insert": "\n"},
    {"insert": "\nThis is"},
    {"attributes": {"code-block": true}, "insert": "\n"},
    {"insert": "Code"},
    {"attributes": {"code-block": true}, "insert": "\n"},
    {"insert": "sdfsdfds"},
    {"attributes": {"blockquote": true}, "insert": "\n"}
]}
JSON;

    public $html = <<<'EOT'
<p>Just text</p>
<blockquote><strong>Quo</strong>te<br>Quote2</blockquote>
<p><br></p>
<pre><code>This is
Code</code></pre>
<blockquote>sdfsdfds</blockquote>
EOT;
}

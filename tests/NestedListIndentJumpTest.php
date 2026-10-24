<?php

namespace nadar\quill\tests;

/**
 * A list element which skips an indent level is wrapped into an empty list element of the skipped level.
 */
class NestedListIndentJumpTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {"insert":"A"},
    {"attributes":{"list":"bullet"},"insert":"\n"},
    {"insert":"B"},
    {"attributes":{"indent":2,"list":"bullet"},"insert":"\n"},
    {"insert":"C"},
    {"attributes":{"list":"bullet"},"insert":"\n"}
]
JSON;

    public $html = <<<'EOT'
<ul><li>A<ul><li><ul><li>B</li></ul></li></ul></li><li>C</li></ul>
EOT;
}

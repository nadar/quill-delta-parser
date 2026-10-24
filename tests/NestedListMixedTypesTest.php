<?php

namespace nadar\quill\tests;

/**
 * A nested list uses the list type of its own elements, not the type of the parent list.
 */
class NestedListMixedTypesTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {"insert":"A"},
    {"attributes":{"list":"bullet"},"insert":"\n"},
    {"insert":"B"},
    {"attributes":{"indent":1,"list":"ordered"},"insert":"\n"},
    {"insert":"C"},
    {"attributes":{"indent":1,"list":"ordered"},"insert":"\n"},
    {"insert":"D"},
    {"attributes":{"list":"bullet"},"insert":"\n"}
]
JSON;

    public $html = <<<'EOT'
<ul><li>A<ol><li>B</li><li>C</li></ol></li><li>D</li></ul>
EOT;
}

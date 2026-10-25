<?php

namespace nadar\quill\tests;

/**
 * Going back from a deeply nested list element must close every nested level.
 */
class NestedListCloseMultipleLevelsTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {"insert":"A"},
    {"attributes":{"list":"bullet"},"insert":"\n"},
    {"insert":"B"},
    {"attributes":{"indent":1,"list":"bullet"},"insert":"\n"},
    {"insert":"C"},
    {"attributes":{"indent":2,"list":"bullet"},"insert":"\n"},
    {"insert":"D"},
    {"attributes":{"indent":3,"list":"bullet"},"insert":"\n"},
    {"insert":"E"},
    {"attributes":{"list":"bullet"},"insert":"\n"}
]
JSON;

    public $html = <<<'EOT'
<ul><li>A<ul><li>B<ul><li>C<ul><li>D</li></ul></li></ul></li></ul></li><li>E</li></ul>
EOT;
}

<?php

namespace nadar\quill\tests;

/**
 * List elements after an empty list element must keep their content.
 */
class ListEmptyItemTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {"insert":"Intro\n"},
    {"attributes":{"list":"bullet"},"insert":"\n"},
    {"insert":"After empty"},
    {"attributes":{"list":"bullet"},"insert":"\n"},
    {"insert":"Last"},
    {"attributes":{"list":"bullet"},"insert":"\n"}
]
JSON;

    public $html = <<<'EOT'
<p>Intro</p><ul><li></li><li>After empty</li><li>Last</li></ul>
EOT;
}

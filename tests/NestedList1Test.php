<?php
namespace nadar\quill\tests;

class NestedList1Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[{"insert":"Foo 1"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"Foo 2"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"Bar 1"},{"attributes":{"indent":1,"list":"bullet"},"insert":"\n"},{"insert":"Bar 2"},{"attributes":{"indent":1,"list":"bullet"},"insert":"\n"}]}
JSON;

    public $html = <<<'EOT'
<ul>
<li>Foo 1</li>
<li>Foo 2
<ul>
<li>Bar 1</li>
<li>Bar 2</li>
</ul>
</li>
</ul>

EOT;
}

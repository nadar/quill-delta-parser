<?php
namespace nadar\quill\tests;

class NestedNestedNestedList1Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[{"insert":"Foo 1"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"Sub 1"},{"attributes":{"indent":1,"list":"bullet"},"insert":"\n"},{"attributes":{"italic":true},"insert":"Sub 2"},{"attributes":{"indent":1,"list":"bullet"},"insert":"\n"},{"insert":"SubSub 1"},{"attributes":{"indent":2,"list":"bullet"},"insert":"\n"},{"attributes":{"bold":true},"insert":"X1"},{"attributes":{"indent":3,"list":"bullet"},"insert":"\n"},{"insert":"X2"},{"attributes":{"indent":3,"list":"bullet"},"insert":"\n"},{"insert":"X3"},{"attributes":{"indent":3,"list":"bullet"},"insert":"\n"},{"insert":"SubSub 2"},{"attributes":{"indent":2,"list":"bullet"},"insert":"\n"},{"insert":"Foo 2"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"Bar 1"},{"attributes":{"indent":1,"list":"bullet"},"insert":"\n"},{"insert":"Bar 2"},{"attributes":{"indent":1,"list":"bullet"},"insert":"\n"}]}
JSON;

    public $html = <<<'EOT'
<ul>
<li>Foo 1
<ul>
<li>Sub 1</li>
<li><em>Sub 2</em>
<ul>
<li>SubSub 1
<ul>
<li><strong>X1</strong></li>
<li>X2</li>
<li>X3</li>
</ul>
</li>
<li>SubSub 2</li>
</ul>
</li>
</ul></li><li>Foo 2
<ul>
<li>Bar 1</li>
<li>Bar 2</li>
</ul>
</li>
</ul>

EOT;
}

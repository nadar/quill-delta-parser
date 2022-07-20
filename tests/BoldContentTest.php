<?php
namespace nadar\quill\tests;

class BoldContentTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[{"attributes":{"bold":true},"insert":"Formatted"},{"insert":"\n"},{"attributes":{"bold":true},"insert":"Heading 1"},{"attributes":{"header":1},"insert":"\n"},{"insert":"list"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"list"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"Heading 2"},{"attributes":{"header":2},"insert":"\n"}]}

JSON;

    public $html = <<<'EOT'
<p><strong>Formatted</strong></p>
<h1><strong>Heading 1</strong></h1>
<ul>
<li>list</li>
<li>list</li>
</ul>
<h2>Heading 2</h2>
EOT;
}

<?php
namespace nadar\quill\tests;

class ListCheckTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[{"insert":"This is a checked element"},{"attributes":{"list":"checked"},"insert":"\n"},{"insert":"This is an unchecked element"},{"attributes":{"list":"unchecked"},"insert":"\n"}]}
JSON;

    public $html = <<<'EOT'
<ul class="list-unstyled">
<li><input type="checkbox" disabled checked><label>This is a checked element</label></li>
<li><input type="checkbox" disabled><label>This is an unchecked element</label></li>
</ul>

EOT;
}

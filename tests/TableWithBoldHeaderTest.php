<?php

namespace nadar\quill\tests;

class TableWithBoldHeaderTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
    "ops": [
        {"insert": "Name", "attributes": {"bold": true}},
        {"insert": "\n", "attributes": {"table": "row-1"}},
        {"insert": "Age", "attributes": {"bold": true}},
        {"insert": "\n", "attributes": {"table": "row-1"}},
        {"insert": "John"},
        {"insert": "\n", "attributes": {"table": "row-2"}},
        {"insert": "25"},
        {"insert": "\n", "attributes": {"table": "row-2"}}
    ]
}
JSON;

    public $html = <<<'EOT'
<table>
<tbody>
<tr>
<td><strong>Name</strong></td>
<td><strong>Age</strong></td>
</tr>
<tr>
<td>John</td>
<td>25</td>
</tr>
</tbody>
</table>
EOT;
}

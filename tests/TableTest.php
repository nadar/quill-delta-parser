<?php

namespace nadar\quill\tests;

class TableTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
    "ops": [
        {"insert": "Name"},
        {"insert": "\n", "attributes": {"table": "row-1"}},
        {"insert": "Age"},
        {"insert": "\n", "attributes": {"table": "row-1"}},
        {"insert": "John"},
        {"insert": "\n", "attributes": {"table": "row-2"}},
        {"insert": "25"},
        {"insert": "\n", "attributes": {"table": "row-2"}},
        {"insert": "Jane"},
        {"insert": "\n", "attributes": {"table": "row-3"}},
        {"insert": "30"},
        {"insert": "\n", "attributes": {"table": "row-3"}}
    ]
}
JSON;

    public $html = <<<'EOT'
<table>
<tbody>
<tr>
<td>Name</td>
<td>Age</td>
</tr>
<tr>
<td>John</td>
<td>25</td>
</tr>
<tr>
<td>Jane</td>
<td>30</td>
</tr>
</tbody>
</table>
EOT;
}

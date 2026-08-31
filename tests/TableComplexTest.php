<?php

namespace nadar\quill\tests;

class TableComplexTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
    "ops": [
        {"insert": "Task Name", "attributes": {"bold": true}},
        {"insert": "\n", "attributes": {"table": "row-1"}},
        {"insert": "Owner", "attributes": {"bold": true}},
        {"insert": "\n", "attributes": {"table": "row-1"}},
        {"insert": "Status", "attributes": {"bold": true}},
        {"insert": "\n", "attributes": {"table": "row-1"}},
        {"insert": "Completion (%)", "attributes": {"bold": true}},
        {"insert": "\n", "attributes": {"align": "center", "table": "row-1"}},
        {"insert": "UI Design"},
        {"insert": "\n", "attributes": {"table": "row-2"}},
        {"insert": "Jeet Kumar"},
        {"insert": "\n", "attributes": {"table": "row-2"}},
        {"insert": "Completed"},
        {"insert": "\n", "attributes": {"table": "row-2"}},
        {"insert": "100%", "attributes": {"italic": true}},
        {"insert": "\n", "attributes": {"align": "center", "table": "row-2"}},
        {"insert": "Backend API"},
        {"insert": "\n", "attributes": {"table": "row-3"}},
        {"insert": "Rahul Sharma"},
        {"insert": "\n", "attributes": {"table": "row-3"}},
        {"insert": "In Progress"},
        {"insert": "\n", "attributes": {"table": "row-3"}},
        {"insert": "65%", "attributes": {"italic": true}},
        {"insert": "\n", "attributes": {"align": "center", "table": "row-3"}}
    ]
}
JSON;

    public $html = <<<'EOT'
<table>
<tbody>
<tr>
<td><strong>Task Name</strong></td>
<td><strong>Owner</strong></td>
<td><strong>Status</strong></td>
<td><strong>Completion (%)</strong></td>
</tr>
<tr>
<td>UI Design</td>
<td>Jeet Kumar</td>
<td>Completed</td>
<td><em>100%</em></td>
</tr>
<tr>
<td>Backend API</td>
<td>Rahul Sharma</td>
<td>In Progress</td>
<td><em>65%</em></td>
</tr>
</tbody>
</table>
EOT;
}

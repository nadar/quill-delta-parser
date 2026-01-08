<?php

namespace nadar\quill\tests;

class TableWithBoldHeaderTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
    "ops": [
        {
            "attributes": {
                "bold": true
            },
            "insert": "Name"
        },
        {
            "attributes": {
                "table-cell-line": {
                    "row": "row-1",
                    "cell": "cell-1",
                    "rowspan": 1,
                    "colspan": 1
                }
            },
            "insert": "\n"
        },
        {
            "attributes": {
                "bold": true
            },
            "insert": "Age"
        },
        {
            "attributes": {
                "table-cell-line": {
                    "row": "row-1",
                    "cell": "cell-2",
                    "rowspan": 1,
                    "colspan": 1
                }
            },
            "insert": "\n"
        },
        {
            "insert": "John"
        },
        {
            "attributes": {
                "table-cell-line": {
                    "row": "row-2",
                    "cell": "cell-1",
                    "rowspan": 1,
                    "colspan": 1
                }
            },
            "insert": "\n"
        },
        {
            "insert": "25"
        },
        {
            "attributes": {
                "table-cell-line": {
                    "row": "row-2",
                    "cell": "cell-2",
                    "rowspan": 1,
                    "colspan": 1
                }
            },
            "insert": "\n"
        }
    ]
}
JSON;

    public $html = <<<'EOT'
<table><tr><td><strong>Name</strong></td><td><strong>Age</strong></td></tr><tr><td>John</td><td>25</td></tr></table>
EOT;
}

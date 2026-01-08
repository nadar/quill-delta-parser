<?php

namespace nadar\quill\tests;

class TableTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
    "ops": [
        {
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
        },
        {
            "insert": "Jane"
        },
        {
            "attributes": {
                "table-cell-line": {
                    "row": "row-3",
                    "cell": "cell-1",
                    "rowspan": 1,
                    "colspan": 1
                }
            },
            "insert": "\n"
        },
        {
            "insert": "30"
        },
        {
            "attributes": {
                "table-cell-line": {
                    "row": "row-3",
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
<table><tr><td>Name</td><td>Age</td></tr><tr><td>John</td><td>25</td></tr><tr><td>Jane</td><td>30</td></tr></table>
EOT;
}

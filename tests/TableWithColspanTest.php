<?php

namespace nadar\quill\tests;

class TableWithColspanTest extends DeltaTestCase
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
                    "colspan": 2
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
<table><tr><td colspan="2">Name</td></tr><tr><td>John</td><td>25</td></tr></table>
EOT;
}

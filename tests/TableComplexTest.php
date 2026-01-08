<?php

namespace nadar\quill\tests;

class TableComplexTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
    "ops": [
        {
            "attributes": {
                "bold": true
            },
            "insert": "Product"
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
            "insert": "Price"
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
            "attributes": {
                "bold": true
            },
            "insert": "Quantity"
        },
        {
            "attributes": {
                "table-cell-line": {
                    "row": "row-1",
                    "cell": "cell-3",
                    "rowspan": 1,
                    "colspan": 1
                }
            },
            "insert": "\n"
        },
        {
            "insert": "Widget "
        },
        {
            "attributes": {
                "italic": true
            },
            "insert": "Pro"
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
            "insert": "$19.99"
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
            "insert": "5"
        },
        {
            "attributes": {
                "table-cell-line": {
                    "row": "row-2",
                    "cell": "cell-3",
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
<table><tr><td><strong>Product</strong></td><td><strong>Price</strong></td><td><strong>Quantity</strong></td></tr><tr><td>Widget <em>Pro</em></td><td>$19.99</td><td>5</td></tr></table>
EOT;
}

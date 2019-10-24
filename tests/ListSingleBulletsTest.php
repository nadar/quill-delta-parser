<?php

namespace nadar\quill\tests;

class ListSingleBullets extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {
        "insert": "line 1\nbullet 1 - 1"
    },
    {
        "attributes": {
            "list": "bullet"
        },
        "insert": "\n"
    },
    {
        "insert": "line 2\nbullet 2 - 1"
    },
    {
        "attributes": {
            "list": "bullet"
        },
        "insert": "\n"
    },
    {
        "insert": "line 3\nbullet 3 - 1"
    },
    {
        "attributes": {
            "list": "bullet"
        },
        "insert": "\n"
    },
    {
        "insert": "line 4\nbullet 4 - 1"
    },
    {
        "attributes": {
            "list": "bullet"
        },
        "insert": "\n"
    },
    {
        "insert": "bullet 4 - 2"
    },
    {
        "attributes": {
            "list": "bullet"
        },
        "insert": "\n"
    },
    {
        "insert": "line 5\n"
    }
]
JSON;

    public $html = <<<'EOT'
<p>line 1</p>
<ul>
<li>bullet 1 - 1</li>
</ul>
<p>line 2</p>
<ul>
<li>bullet 2 - 1</li>
</ul>
<p>line 3</p>
<ul>
<li>bullet 3 - 1</li>
</ul>
<p>line 4</p>
<ul>
<li>bullet 4 - 1</li>
<li>bullet 4 - 2</li>
</ul>
<p>line 5</p>
EOT;
}

<?php

namespace nadar\quill\tests;

/**
 * Test indent attribute support with ordered lists in combined ops format.
 */
class IndentAttributeOrderedTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    { "insert": "First\n", "attributes": { "list": "ordered" } },
    { "insert": "First Sub\n", "attributes": { "list": "ordered", "indent": 1 } },
    { "insert": "Second\n", "attributes": { "list": "ordered" } }
  ]
}
JSON;

    public $html = <<<'EOT'
<ol><li>First<ol><li>First Sub</li></ol></li><li>Second</li></ol>
EOT;
}

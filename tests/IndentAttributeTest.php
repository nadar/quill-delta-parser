<?php

namespace nadar\quill\tests;

/**
 * Test indent attribute support with combined ops format.
 *
 * This test validates the fix for the issue where Delta ops with both
 * content and newline in a single insert (e.g., {"insert": "Item 1\n", "attributes": {"list": "bullet", "indent": 1}})
 * were not being parsed correctly, resulting in empty list items.
 */
class IndentAttributeTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    { "insert": "Item 1\n", "attributes": { "list": "bullet" } },
    { "insert": "Item 1.1\n", "attributes": { "list": "bullet", "indent": 1 } },
    { "insert": "Item 1.1.1\n", "attributes": { "list": "bullet", "indent": 2 } }
  ]
}
JSON;

    public $html = <<<'EOT'
<ul><li>Item 1<ul><li>Item 1.1<ul><li>Item 1.1.1</li></ul></li></ul></li></ul>
EOT;
}

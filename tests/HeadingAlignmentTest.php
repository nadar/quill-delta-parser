<?php
namespace nadar\quill\tests;

/**
 * Test heading with alignment attributes.
 * 
 * This test addresses the issue where headers with alignment attributes
 * were being rendered as <p> tags instead of <h1>-<h6> tags.
 */
class HeadingAlignmentTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    {
      "attributes": {
        "background": "transparent",
        "color": "#000000"
      },
      "insert": "Header"
    },
    {
      "attributes": {
        "align": "center",
        "header": 1
      },
      "insert": "\n"
    }
  ]
}
JSON;

    public $html = <<<'EOT'
<h1 style="text-align: center;"><span style="background-color:transparent"><span style="color:#000000">Header</span></span></h1>
EOT;
}

<?php
namespace nadar\quill\tests;

/**
 * Test various heading levels with different alignment options.
 */
class HeadingAlignmentVariationsTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{
  "ops": [
    {
      "insert": "Right H2"
    },
    {
      "attributes": {
        "align": "right",
        "header": 2
      },
      "insert": "\n"
    },
    {
      "attributes": {
        "bold": true
      },
      "insert": "Justified H4"
    },
    {
      "attributes": {
        "align": "justify",
        "header": 4
      },
      "insert": "\n"
    },
    {
      "insert": "Left H3"
    },
    {
      "attributes": {
        "align": "left",
        "header": 3
      },
      "insert": "\n"
    }
  ]
}
JSON;

    public $html = <<<'EOT'
<h2 style="text-align: right;">Right H2</h2><h4 style="text-align: justify;"><strong>Justified H4</strong></h4><h3 style="text-align: left;">Left H3</h3>
EOT;
}

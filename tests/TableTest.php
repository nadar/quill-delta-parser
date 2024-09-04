<?php

namespace nadar\quill\tests;

class TextAttributesTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
  {
    "insert": "1111"
  },
  {
    "attributes": {
      "table": "row-nffr"
    },
    "insert": "\n"
  },
  {
    "insert": "2222"
  },
  {
    "attributes": {
      "table": "row-nffr"
    },
    "insert": "\n"
  },
  {
    "insert": "3333"
  },
  {
    "attributes": {
      "table": "row-nffr"
    },
    "insert": "\n"
  },
  {
    "insert": "4444"
  },
  {
    "attributes": {
      "table": "row-nffr"
    },
    "insert": "\n"
  },
  {
    "insert": "5555"
  },
  {
    "attributes": {
      "table": "row-nffr"
    },
    "insert": "\n"
  },
  {
    "attributes": {
      "table": "row-3or1"
    },
    "insert": "\n\n\n\n\n"
  },
  {
    "attributes": {
      "table": "true"
    },
    "insert": "\n\n\n\n\n"
  },
  {
    "insert": "\nThis text is outside the table.\n"
  }
]
JSON;

    public $html = <<<'EOT'
    <p><span style="color:#27333a"><strong>Was: </strong></span><span style="color:#27333a">Mi</span></p><p><span style="color:#27333a"><strong>Wann:</strong></span><span style="color:#27333a"> März</span></p><p><span style="color:#27333a"><strong>Wo:</strong></span><span style="color:#27333a"> Aarau</span></p>
EOT;
}

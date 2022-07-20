<?php

namespace nadar\quill\tests;

class TextAttributesTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {
      "attributes": {
        "color": "#27333a",
        "bold": true
      },
      "insert": "Was: "
    },
    {
      "attributes": {
        "color": "#27333a"
      },
      "insert": "Mi"
    },
    {
      "insert": "\n"
    },
    {
      "attributes": {
        "color": "#27333a",
        "bold": true
      },
      "insert": "Wann:"
    },
    {
      "attributes": {
        "color": "#27333a"
      },
      "insert": " März"
    },
    {
      "insert": "\n"
    },
    {
      "attributes": {
        "color": "#27333a",
        "bold": true
      },
      "insert": "Wo:"
    },
    {
      "attributes": {
        "color": "#27333a"
      },
      "insert": " Aarau"
    },
    {
        "insert": "\n"
    }]
JSON;

    public $html = <<<'EOT'
    <p><span style="color:#27333a"><strong>Was: </strong></span><span style="color:#27333a">Mi</span></p>
<p><span style="color:#27333a"><strong>Wann:</strong></span><span style="color:#27333a"> März</span></p>
<p><span style="color:#27333a"><strong>Wo:</strong></span><span style="color:#27333a"> Aarau</span></p>
EOT;
}

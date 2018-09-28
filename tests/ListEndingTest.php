<?php

namespace nadar\quill\tests;

class ListEndingTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {
      "insert": "Intro\n\n"
    },
    {
      "attributes": {
        "bold": true
      },
      "insert": "Infobox"
    },
    {
      "insert": "\nA"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "B"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "C"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "D"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "insert": "E"
    },
    {
      "attributes": {
        "list": "bullet"
      },
      "insert": "\n"
    },
    {
      "attributes": {
        "bold": true
      },
      "insert": "Infos "
    },
    {
      "attributes": {
        "bold": true,
        "link": "https://luya.io"
      },
      "insert": "luya.io"
    },
    {
      "insert": "\n\nFooter\n"
    }
  ]
JSON;

public $html = <<<'EOT'
<p>Intro</p>
<p><br></p>
<p><strong>Infobox</strong></p>
<ul>
<li>A</li>
<li>B</li>
<li>C</li>
<li>D</li>
<li>E</li>
</ul>
<p><strong>Infos </strong><a href="https://luya.io" target="_blank"><strong>luya.io</strong></a></p>
<p><br></p>
<p>Footer</p>
EOT;
}
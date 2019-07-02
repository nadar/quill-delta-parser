<?php
namespace nadar\quill\tests;

class BoldContentTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops": [
  {"insert": "Heading 1","attributes": {"bold": true}},
  {"insert": "\n","attributes": {"header": 1}},
  {"insert": "list"},
  {"insert": "\n","attributes": {"list": "bullet"}},
  {"insert": "list"},{"attributes": {"list": "bullet"},"insert": "\n"},
  {"insert": "Heading 2"},{"attributes": {"header": 2},"insert": "\n"}
]}

JSON;

    public $html = <<<'EOT'
<h1><strong>Heading 1</strong></h1><ul><li>list</li><li>list</li></ul><h2>Heading 2</h2>
EOT;
}

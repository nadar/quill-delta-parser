<?php
namespace nadar\quill\tests;

class HeadingContentTest extends DeltaTestCase
{
	public $json = <<<'JSON'
{"ops":[
  {"insert":"xyz","attributes":{"bold":true}},
  {"insert":"\n"},
  {"insert":"regular"},
  {"insert":"bold","attributes":{"bold":true}},
  {"insert":"italic", "attributes":{"italic":true}},
  {"insert":"\n","attributes":{"header":1}},
  {"insert":"xyz\n"}
]}

JSON;
	
	public $html = <<<'EOT'
<p><strong>xyz</strong></p><h1>regular<strong>bold</strong><em>italic</em></h1><p>xyz</p>
EOT;
}

<?php
namespace nadar\quill\tests;

class BlockquoteContentTest extends DeltaTestCase
{
	public $json = <<<'JSON'
{"ops":[
  {"insert":"xyz","attributes":{"bold":true}},
  {"insert":"\n"},
  {"insert":"regular"},
  {"insert":"bold","attributes":{"bold":true}},
  {"insert":"italic", "attributes":{"italic":true}},
  {"insert":"\n","attributes":{"blockquote":true}},
  {"insert":"xyz\n"}
]}

JSON;
	
	public $html = <<<'EOT'
<p><strong>xyz</strong></p><blockquote>regular<strong>bold</strong><em>italic</em></blockquote><p>xyz</p>
EOT;
}

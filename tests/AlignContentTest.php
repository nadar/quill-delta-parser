<?php
namespace nadar\quill\tests;

class AlignContentTest extends DeltaTestCase
{
	public $json = <<<'JSON'
{"ops":[
  {"insert":"Lorem "},
  {"attributes":{"bold":true},"insert":"Ipsum"},
  {"insert":" Dolor "},
  {"attributes":{"underline":true,"italic":true},"insert":"Sit"},
  {"insert":" Amet"},
  {"attributes":{"align":"center"},"insert":"\n"},
  {"insert":"This images is right aligned : "},
  {"insert":{"image":"https://example.com/image.jpg"}},
  {"attributes":{"align":"right"},"insert":"\n"},
  {"insert":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer "},
  {"attributes":{"bold":true},"insert":"Lorem"},
  {"insert":" nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla "},
  {"attributes":{"bold":true},"insert":"cursus"},
  {"insert":" quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus "},
  {"attributes":{"bold":true},"insert":"sagittis"},
  {"insert":" sed augue semper porta. Mauris massa. Vestibulum "},
  {"attributes":{"italic":true},"insert":"quis"},
  {"insert":" lacinia arcu "},
  {"attributes":{"italic":true},"insert":"sem"},
  {"insert":" eget "},
  {"attributes":{"bold":true},"insert":"augue"},
  {"insert":" nulla. Class aptent taciti sociosqu "},
  {"attributes":{"bold":true},"insert":"massa."},
  {"insert":" ad litora torquent per conubia nostra, per "},
  {"attributes":{"italic":true},"insert":"porta."},
  {"insert":" inceptos himenaeos. Curabitur "},
  {"attributes":{"bold":true},"insert":"taciti"},
  {"insert":" sodales ligula in "},
  {"attributes":{"bold":true},"insert":"conubia"},
  {"insert":" libero."},
  {"attributes":{"align":"justify"},"insert":"\n"}
]}
JSON;

	public $html = <<<'EOT'
<p style="text-align: center;">Lorem <strong>Ipsum</strong> Dolor <u><em>Sit</em></u> Amet</p>
<p style="text-align: right;">This images is right aligned : <img src="https://example.com/image.jpg" alt="" class="img-responsive img-fluid" /></p>
<p style="text-align: justify;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer <strong>Lorem</strong> nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla <strong>cursus</strong> quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus <strong>sagittis</strong> sed augue semper porta. Mauris massa. Vestibulum <em>quis</em> lacinia arcu <em>sem</em> eget <strong>augue</strong> nulla. Class aptent taciti sociosqu <strong>massa.</strong> ad litora torquent per conubia nostra, per <em>porta.</em> inceptos himenaeos. Curabitur <strong>taciti</strong> sodales ligula in <strong>conubia</strong> libero.</p>
EOT;
}

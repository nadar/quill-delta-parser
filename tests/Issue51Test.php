<?php
namespace nadar\quill\tests;

class Issue51Test extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[{"attributes":{"italic":true},"insert":{"image":"https://pathtoimage.jpg"}},{"insert":"\n"}]}
JSON;

    public $html = <<<'EOT'
    <p><em><img src="https://pathtoimage.jpg" alt="" class="img-responsive img-fluid" /></em></p>
EOT;
}

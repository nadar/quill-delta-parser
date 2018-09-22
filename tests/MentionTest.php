<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\listener\Mention;

/**
 * @see https://www.transmute-coffee.com/php-quill-renderer.php#demo
 */
class MentionTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[
  {"insert":"Hello "},
  {"insert":{"mention":{"id":"1","value":"John","denotationChar":"@"}}},
  {"insert":"!\n\nHello "},
  {"attributes":{"bold":true},"insert":"Jane"},
  {"insert":"!\n"}]}
JSON;
    public $html = <<<'EOT'
<p>Hello John!</p>
<p><br></p>
<p>Hello <strong>Jane</strong>!</p>
EOT;

    public function getLexer()
    {
        $lexer = new Lexer($this->json);
        $lexer->registerListener(new Mention);

        return $lexer;
    }
}

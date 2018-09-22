<?php

namespace nadar\quill\tests;

use PHPUnit\Framework\TestCase;
use nadar\quill\Lexer;
use nadar\quill\listener\Text;
use nadar\quill\listener\Heading;
use nadar\quill\listener\Bold;

/**
 * @see https://www.transmute-coffee.com/php-quill-renderer.php#demo
 */
class EndOfNewlineTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[
  {"insert":"\n\nFooter.\n"}    
]}
JSON;
    public $html = <<<'EOT'
<p><br></p>
<p><br></p>
<p>Footer.</p>
EOT;
}

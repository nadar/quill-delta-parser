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
class ComplexDeltaTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops":[
  {"insert":"\nIntro Text.\n\n"},
  {"attributes":{"bold":true},"insert":"Was"},
  {"insert":": Ausstellung\n"},
  {"attributes":{"bold":true},"insert":"Wann"},
  {"insert":": 27.9.2018\n"},
  {"attributes":{"bold":true},"insert":"Wo"},
  {"insert":": Aarau\n"},
  {"insert":"\nFooter.\n"}    
]}
JSON;

    public $html = <<<'EOT'
<p><br></p>
<p>Intro Text.</p>
<p><br></p>
<p><strong>Was</strong>: Ausstellung</p>
<p><strong>Wann</strong>: 27.9.2018</p>
<p><strong>Wo</strong>: Aarau</p>
<p><br></p>
<p>Footer.</p>
EOT;
}

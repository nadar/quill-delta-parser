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
class ComplexDeltaTest extends TestCase
{
    public function testOutput()
    {
        $json = <<<'JSON'
{"ops":[
  {"insert":"\nAlberto Giacometti (1901–1966) u... Zustände erahnen.\n\n"},
  {"attributes":{"bold":true},"insert":"Was"},
  {"insert":": Ausstellung «Bacon – Giacometti»\n"},
  {"attributes":{"bold":true},"insert":"Wann"},
  {"insert":": Bis 2. September 2018\n"},
  {"attributes":{"bold":true},"insert":"Wo"},
  {"insert":": Fondation Beyeler, Riehen\n"},
  {"attributes":{"bold":true},"insert":"Öffnungszeiten"},
  {"insert":": Donnerstag bis Dienstag, 10 bis 18 Uhr | Mittwoch, 10 bis 20 Uhr \n"},
  {"attributes":{"bold":true},"insert":"Eintritt"},
  {"insert":": CHF 28\n"},
  {"insert":"\n\nDieser Beitrag ist in Kooperation mit der Fondation Beyeler entstanden.\n"}    
]}
JSON;

        $same = <<<'EOT'
<p><br></p>
<p>Alberto Giacometti (1901–1966) u... Zustände erahnen.</p>
<p><strong>Was</strong>: Ausstellung «Bacon – Giacometti»</p>
EOT;
        
        $parser = new Lexer($json);
        $parser->initBuiltInListeners();

        $this->assertSame(trim(str_replace(PHP_EOL, '', $same)), trim($parser->render()));
    }

}
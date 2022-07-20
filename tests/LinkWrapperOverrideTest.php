<?php

namespace nadar\quill\tests;

use nadar\quill\listener\Link;

class LinkWrapperOverrideTest extends DeltaTestCase
{
    public $json = '[{"attributes":{"link":"https://luya.io"},"insert":"luya.io"},{"insert":" test\n\nFooter\n"}]';

    public $html = '<p><a href="https://luya.io">luya.io</a> test</p>'.PHP_EOL.'<p><br></p>'.PHP_EOL.'<p>Footer</p>'.PHP_EOL;

    public function listeners(\nadar\quill\Lexer $lexer)
    {
        $link = new Link();
        $link->wrapperOpen = '<a href="{link}">';
        $lexer->registerListener($link);
    }
}

<?php

namespace nadar\quill\tests;

use nadar\quill\listener\Link;

class LinkWrapperOverrideTest extends DeltaTestCase
{
    public $json = '[{"attributes":{"link":"https://luya.io"},"insert":"luya.io"},{"insert":" test\n\nFooter\n"}]';

    public $html = '<p><a href="https://luya.io">luya.io</a> test</p><p><br></p><p>Footer</p>';

    public function listeners(\nadar\quill\Lexer $lexer)
    {
        $link = new Link();
        $link->wrapper = '<a href="{link}">{text}</a>';
        $lexer->registerListener($link);
    }
}

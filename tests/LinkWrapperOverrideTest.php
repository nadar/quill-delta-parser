<?php

namespace nadar\quill\tests;

use nadar\quill\listener\Link;

class LinkWrapperOverrideTest extends DeltaTestCase
{
    public $json = [
        [
            'attributes' => [
                'link' => 'https://luya.io',
            ],
            'insert' => 'luya.io',
        ],
        [
            'insert' => ' test'.PHP_EOL.PHP_EOL.'Footer'.PHP_EOL,
        ],
    ];

    public $html = '<p><a href="https://luya.io">luya.io</a> test</p><p><br></p><p>Footer</p>';

    public function listeners(\nadar\quill\Lexer $lexer)
    {
        $link = new Link();
        $link->wrapperOpen = '<a href="{link}">';
        $lexer->registerListener($link);
    }
}

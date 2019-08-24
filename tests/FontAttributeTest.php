<?php

namespace nadar\quill\tests;

use nadar\quill\listener\Font;

class FontAttributeTest extends DeltaTestCase
{
    public $json = '{"ops":[{"insert":"Lorem "},{"attributes":{"font":"serif"},"insert":"Ipsum"},{"insert":" Dolor. "},{"attributes":{"font":"monospace"},"insert":"Sit"},{"insert":" Amet.\n"}]}';

    public $html = '<p>Lorem Ipsum Dolor. Sit Amet.</p>';

    public function listeners(\nadar\quill\Lexer $lexer)
    {
        $font = new Font();
        $font->ignore = true;
        $lexer->registerListener($font);
    }
}
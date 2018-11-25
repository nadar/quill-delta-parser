<?php

namespace nadar\quill\tests;

use PHPUnit\Framework\TestCase;
use nadar\quill\Lexer;
use nadar\quill\listener\Text;
use nadar\quill\listener\Heading;
use nadar\quill\listener\Bold;
use nadar\quill\Debug;

/**
 * 1. create delta: https://quilljs.com/docs/delta/
 * 2. minify json: https://www.cleancss.com/json-minify/
 */
class ParserTest extends TestCase
{
    public $asserts = [
        '<p><a href="https://luya.io" target="_blank">luya.io</a> test</p><p><br></p><p>Footer</p>' => '[{"attributes":{"link":"https://luya.io"},"insert":"luya.io"},{"insert":" test\n\nFooter\n"}]',
        '<p><a href="https://luya.io" target="_blank">luya.io</a></p><p><br></p><p>Ende.</p>' => '[{"attributes":{"link":"https://luya.io"},"insert":"luya.io"},{"insert":"\n\nEnde.\n"}]',
        '<p>Start</p><p><br></p><p>Ende</p>' => '[{"insert":"Start\n"}, {"insert":"\n"}, {"insert":"Ende\n"}]',
        '<ul><li>Foo</li><li>Bar</li></ul><p><br></p>' => '[{"insert": "Foo"},{"attributes": { "list": "bullet" },"insert": "\n"},{"insert": "Bar"},{"attributes": { "list": "bullet"},"insert": "\n"},{"insert": "\n"}]',
        '<p>Hallo</p><p>Wie</p><p>Gehts?</p>' => '{"ops": [{"insert": "Hallo\nWie\nGehts?\n"}]}',
        '<p>Hallo</p><p>Wie</p><p><br></p><p>Shift</p><p>Enter</p>' => '[{"insert": "Hallo\nWie\n\nShift\nEnter\n"}]',
        '<h1>Title</h1><p>Text with <strong>bold</strong> element.</p>' => '{"ops":[{"insert":"Title"},{"attributes":{"header":1},"insert":"\n"},{"insert":"Text with "},{"attributes":{"bold":true},"insert":"bold"},{"insert":" element.\n"}]}',
        '<p><em>Italic</em></p>' => '[{"attributes":{"italic":true},"insert":"Italic"},{"insert":"\n"}]',
        '<blockquote><em>text</em></blockquote>' => '[{"attributes":{"italic":true},"insert":"text"},{"attributes":{"blockquote":true},"insert":"\n"}]',
        '<p><em>Italic</em> <strong>Bold</strong> <em><strong>BoldItalic</strong></em> Append</p>' => '[{"attributes":{"italic":true},"insert":"Italic"},{"insert":" "},{"attributes":{"bold":true},"insert":"Bold"},{"insert":" "},{"attributes":{"italic":true,"bold":true},"insert":"BoldItalic"},{"insert":" Append\n"}]',
        '<p>Before</p><p><strong>Bold </strong><em><strong>Italic</strong></em> without.</p><p>After</p>' => '[{"insert":"Before\n"},{"attributes":{"bold":true},"insert":"Bold "},{"attributes":{"italic":true,"bold":true},"insert":"Italic"},{"insert":" without.\nAfter\n"}]',
        '<p><del>strike</del></p>' => '[{"attributes":{"strike":true}, "insert" : "strike"},{"insert":"\n"}]',
        '<p><u>Underline</u></p>' => '[{"attributes":{"underline":true}, "insert" : "Underline"},{"insert":"\n"}]',
        '<ol><li>Its <strong>bold</strong></li><li>Its <em>italic</em></li></ol>' => '[{"insert":"Its "},{"attributes":{"bold":true},"insert":"bold"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"Its "},{"attributes":{"italic":true},"insert":"italic"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>intro <strong>bold</strong>!</p><ul><li>elmn 1</li><li>elmn 2</li></ul><p>text</p><ul><li>elmn <strong>bold</strong> a</li><li>elmn b</li></ul>' => '[{"insert":"intro "},{"attributes":{"bold":true},"insert":"bold"},{"insert":"!\nelmn 1"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"elmn 2"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"text\nelmn "},{"attributes":{"bold":true},"insert":"bold"},{"insert":" a"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"elmn b"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<h1>Head<strong>ing</strong></h1>' => '[{"insert":"Head"},{"attributes":{"bold":true},"insert":"ing"},{"attributes":{"header":1},"insert":"\n"}]',
        '<blockquote>Wichtig <strong>Fett</strong></blockquote>' => '[{"insert":"Wichtig "},{"attributes":{"bold":true},"insert":"Fett"},{"attributes":{"blockquote":true},"insert":"\n"}]',
        '<blockquote>Quote</blockquote>' => '[{"insert":"Quote"},{"attributes":{"blockquote":true},"insert":"\n"}]',
        '<h1>Heading 1</h1>' => '[{"insert":"Heading 1"},{"attributes":{"header":1},"insert":"\n"}]',
        '<h1>Hello</h1><p><br></p><p>This is the php quill <strong>parser</strong>!</p>' => '[{"insert":"Hello"},{"attributes":{"header":1},"insert":"\n"},{"insert":"\nThis is the php quill "},{"attributes":{"bold":true},"insert":"parser"},{"insert":"!\n"}]',
        '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/Ybq878PMe_U?showinfo=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>' => '[{"insert":{"video":"https://www.youtube.com/embed/Ybq878PMe_U?showinfo=0"}}]',
    ];

    public function testHtmlIsEqual()
    {
        foreach ($this->asserts as $e => $j) {
            $parser = new Lexer($j);

            $this->assertTrue(is_array($parser->getJsonArray()));
            $this->assertSame($e, $parser->render());

            unset($parser);
        }
    }

    public function testDebugPrint()
    {
        $lexer = new Lexer([["attributes" => ['none' => true], "insert" => "not"], ["insert" => "f\nfoo\n"]]);
        $this->assertSame('<p>f</p><p>foo</p>', $lexer->render());
        
        $debug = new Debug($lexer);
        $this->assertNotNull($debug->debugPrint());
    }
}

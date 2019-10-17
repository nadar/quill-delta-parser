<?php

namespace nadar\quill\tests;

use PHPUnit\Framework\TestCase;
use nadar\quill\Lexer;
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
        '<p><img src="https://example.com/image.jpg" alt="" class="img-responsive img-fluid" />text</p>' => '[{"insert": {"image": "https://example.com/image.jpg"}},{"insert": "text\n"}]',
        '<p>before1</p><p><img src="https://example.com/image.jpg" alt="" class="img-responsive img-fluid" /></p><p>after</p>' => '[{"insert": "before1\n"},{"insert": {"image": "https://example.com/image.jpg"}},{"insert": "\nafter\n"}]',
        '<p>before2<img src="https://example.com/image.jpg" alt="" class="img-responsive img-fluid" />after</p>' => '[{"insert": "before2"},{"insert": {"image": "https://example.com/image.jpg"}},{"insert": "after\n"}]',
        '<ol><li>Title <strong>bold</strong></li></ol>' => '[{"insert":"Title "},{"attributes":{"bold":true},"insert":"bold"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<ol><li>List content</li></ol><p>afternewline</p>' => '[{"insert":"List content"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"afternewline\n"}]',
        '<ol><li><strong>bold</strong></li><li><em>italic</em></li><li><u>underline</u></li><li><del>strike</del></li></ol><p><br></p><p>normal Text</p>' => '{"ops":[{"attributes":{"bold":true},"insert":"bold"},{"attributes":{"list":"ordered"},"insert":"\n"},{"attributes":{"italic":true},"insert":"italic"},{"attributes":{"list":"ordered"},"insert":"\n"},{"attributes":{"underline":true},"insert":"underline"},{"attributes":{"list":"ordered"},"insert":"\n"},{"attributes":{"strike":true},"insert":"strike"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"\nnormal Text\n"}]}',
        '<ul><li><strong>foo</strong></li><li><em>bar</em></li></ul>' => '[{"attributes":{"bold":true},"insert":"foo"},{"attributes":{"list":"bullet"},"insert":"\n"},{"attributes":{"italic":true},"insert":"bar"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<ul><li>a <strong>b</strong> c</li></ul>' => '[{"insert":"a "},{"attributes":{"bold":true},"insert":"b"},{"insert":" c"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<ul><li>a <strong>b</strong> c</li><li><u>u</u></li><li><u>i</u>talic</li></ul><p>newline</p>' => '[{"insert":"a "},{"attributes":{"bold":true},"insert":"b"},{"insert":" c"},{"attributes":{"list":"bullet"},"insert":"\n"},{"attributes":{"underline":true},"insert":"u"},{"attributes":{"list":"bullet"},"insert":"\n"},{"attributes":{"underline":true},"insert":"i"},{"insert":"talic"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"newline\n"}]',
        '<ul><li><strong>a </strong>a</li><li>b</li></ul><p>paragraph</p><ul><li>c</li><li>d <strong>d</strong></li></ul>' => '[{"attributes":{"bold":true},"insert":"a "},{"insert":"a"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"b"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"paragraph\nc"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"d "},{"attributes":{"bold":true},"insert":"d"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<ol><li>item 1</li><li>item 2</li><li>item 3</li></ol>' => '[{"insert":"item 1"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"item 2"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"item 3"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<ul><li>item 1</li><li>item 2</li><li>item 3</li></ul>' => '[{"insert":"item 1"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"item 2"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"item 3"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<p>Text</p><ol><li>List Item 1</li></ol>' => '[{"insert":"Text\nList Item 1"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>Text</p><ol><li><strong>List Item 1</strong></li></ol>' => '[{"insert":"Text\n"},{"attributes":{"bold":true},"insert":"List Item 1"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>Text</p><ol><li><strong>List </strong>Item<strong> 1</strong></li></ol>' => '[{"insert":"Text\n"},{"attributes":{"bold":true},"insert":"List "},{"insert":"Item"},{"attributes":{"bold":true},"insert":" 1"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>Text</p><ol><li><strong>List </strong>Item<strong> 1</strong></li><li>Item 2</li></ol>' => '[{"insert":"Text\n"},{"attributes":{"bold":true},"insert":"List "},{"insert":"Item"},{"attributes":{"bold":true},"insert":" 1"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"Item 2"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<ol><li><strong>List </strong>Item<strong> 1</strong></li><li>Item 2</li></ol>' => '[{"attributes":{"bold":true},"insert":"List "},{"insert":"Item"},{"attributes":{"bold":true},"insert":" 1"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"Item 2"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>text</p><ul><li>A</li><li><strong>B</strong></li></ul><p>text2</p><ul><li><em>C</em></li><li>D</li></ul><ol><li>E</li><li>F</li></ol>' => '{"ops":[{"insert":"text\nA"},{"attributes":{"list":"bullet"},"insert":"\n"},{"attributes":{"bold":true},"insert":"B"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"text2\n"},{"attributes":{"italic":true},"insert":"C"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"D"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"E"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"F"},{"attributes":{"list":"ordered"},"insert":"\n"}]}',
        '<ul><li>A</li><li>B</li></ul><p>Paragraph with <strong>bold</strong></p><ol><li>Y1 <em>Y2</em> Y3</li><li>X1 X2 <strong>X3</strong></li></ol><p>morre <strong>text</strong></p>' => '[{"insert":"A"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"B"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"Paragraph with "},{"attributes":{"bold":true},"insert":"bold"},{"insert":"\nY1 "},{"attributes":{"italic":true},"insert":"Y2"},{"insert":" Y3"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"X1 X2 "},{"attributes":{"bold":true},"insert":"X3"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"morre "},{"attributes":{"bold":true},"insert":"text"},{"insert":"\n"}]',
        '<p>Lorem <span style="font-family: serif;">Ipsum</span> Dolor. <span style="font-family: monospace;">Sit</span> Amet.</p>' => '{"ops":[{"insert":"Lorem "},{"attributes":{"font":"serif"},"insert":"Ipsum"},{"insert":" Dolor. "},{"attributes":{"font":"monospace"},"insert":"Sit"},{"insert":" Amet.\n"}]}',
        '<h2></h2><h2>title</h2><p>text</p>' => '{"ops":[{"attributes":{"header":2},"insert":"\n"},{"insert":"title"},{"attributes":{"header":2},"insert":"\n"},{"insert":"text\n"}]}',
        '<p>lorem <sub>ipsum</sub> dolor <sup>sit</sup> amet</p>' => '{"ops":[{"insert":"lorem "},{"attributes":{"script":"sub"},"insert":"ipsum"},{"insert":" dolor "},{"attributes":{"script":"super"},"insert":"sit"},{"insert":" amet\n"}]}',
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

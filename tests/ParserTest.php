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
        '<p><a href="https://luya.io" target="_blank">luya.io</a> test</p>'.PHP_EOL.'<p><br></p>'.PHP_EOL.'<p>Footer</p>'.PHP_EOL => '[{"attributes":{"link":"https://luya.io"},"insert":"luya.io"},{"insert":" test\n\nFooter\n"}]',
        '<p><a href="https://luya.io" target="_blank">luya.io</a></p>'.PHP_EOL.'<p><br></p>'.PHP_EOL.'<p>Ende.</p>'.PHP_EOL => '[{"attributes":{"link":"https://luya.io"},"insert":"luya.io"},{"insert":"\n\nEnde.\n"}]',
        '<p>Start</p>'.PHP_EOL.'<p><br></p>'.PHP_EOL.'<p>Ende</p>'.PHP_EOL => '[{"insert":"Start\n"}, {"insert":"\n"}, {"insert":"Ende\n"}]',
        '<ul>'.PHP_EOL.'<li>Foo</li>'.PHP_EOL.'<li>Bar</li>'.PHP_EOL.'</ul>'.PHP_EOL.'<p><br></p>'.PHP_EOL => '[{"insert": "Foo"},{"attributes": { "list": "bullet" },"insert": "\n"},{"insert": "Bar"},{"attributes": { "list": "bullet"},"insert": "\n"},{"insert": "\n"}]',
        '<p>Hallo</p>'.PHP_EOL.'<p>Wie</p>'.PHP_EOL.'<p>Gehts?</p>'.PHP_EOL => '{"ops": [{"insert": "Hallo\nWie\nGehts?\n"}]}',
        '<p>Hallo</p>'.PHP_EOL.'<p>Wie</p>'.PHP_EOL.'<p><br></p>'.PHP_EOL.'<p>Shift</p>'.PHP_EOL.'<p>Enter</p>'.PHP_EOL => '[{"insert": "Hallo\nWie\n\nShift\nEnter\n"}]',
        '<h1>Title</h1>'.PHP_EOL.'<p>Text with <strong>bold</strong> element.</p>'.PHP_EOL => '{"ops":[{"insert":"Title"},{"attributes":{"header":1},"insert":"\n"},{"insert":"Text with "},{"attributes":{"bold":true},"insert":"bold"},{"insert":" element.\n"}]}',
        '<p><em>Italic</em></p>'.PHP_EOL => '[{"attributes":{"italic":true},"insert":"Italic"},{"insert":"\n"}]',
        '<blockquote><em>text</em></blockquote>'.PHP_EOL => '[{"attributes":{"italic":true},"insert":"text"},{"attributes":{"blockquote":true},"insert":"\n"}]',
        '<p><em>Italic</em> <strong>Bold</strong> <em><strong>BoldItalic</strong></em> Append</p>'.PHP_EOL => '[{"attributes":{"italic":true},"insert":"Italic"},{"insert":" "},{"attributes":{"bold":true},"insert":"Bold"},{"insert":" "},{"attributes":{"italic":true,"bold":true},"insert":"BoldItalic"},{"insert":" Append\n"}]',
        '<p>Before</p>'.PHP_EOL.'<p><strong>Bold </strong><em><strong>Italic</strong></em> without.</p>'.PHP_EOL.'<p>After</p>'.PHP_EOL => '[{"insert":"Before\n"},{"attributes":{"bold":true},"insert":"Bold "},{"attributes":{"italic":true,"bold":true},"insert":"Italic"},{"insert":" without.\nAfter\n"}]',
        '<p><del>strike</del></p>'.PHP_EOL => '[{"attributes":{"strike":true}, "insert" : "strike"},{"insert":"\n"}]',
        '<p><u>Underline</u></p>'.PHP_EOL => '[{"attributes":{"underline":true}, "insert" : "Underline"},{"insert":"\n"}]',
        '<ol>'.PHP_EOL.'<li>Its <strong>bold</strong></li>'.PHP_EOL.'<li>Its <em>italic</em></li>'.PHP_EOL.'</ol>'.PHP_EOL => '[{"insert":"Its "},{"attributes":{"bold":true},"insert":"bold"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"Its "},{"attributes":{"italic":true},"insert":"italic"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>intro <strong>bold</strong>!</p>'.PHP_EOL.'<ul>'.PHP_EOL.'<li>elmn 1</li>'.PHP_EOL.'<li>elmn 2</li>'.PHP_EOL.'</ul>'.PHP_EOL.'<p>text</p>'.PHP_EOL.'<ul>'.PHP_EOL.'<li>elmn <strong>bold</strong> a</li>'.PHP_EOL.'<li>elmn b</li>'.PHP_EOL.'</ul>'.PHP_EOL => '[{"insert":"intro "},{"attributes":{"bold":true},"insert":"bold"},{"insert":"!\nelmn 1"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"elmn 2"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"text\nelmn "},{"attributes":{"bold":true},"insert":"bold"},{"insert":" a"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"elmn b"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<h1>Head<strong>ing</strong></h1>'.PHP_EOL => '[{"insert":"Head"},{"attributes":{"bold":true},"insert":"ing"},{"attributes":{"header":1},"insert":"\n"}]',
        '<blockquote>Wichtig <strong>Fett</strong></blockquote>'.PHP_EOL => '[{"insert":"Wichtig "},{"attributes":{"bold":true},"insert":"Fett"},{"attributes":{"blockquote":true},"insert":"\n"}]',
        '<blockquote>Quote</blockquote>'.PHP_EOL => '[{"insert":"Quote"},{"attributes":{"blockquote":true},"insert":"\n"}]',
        '<h1>Heading 1</h1>'.PHP_EOL => '[{"insert":"Heading 1"},{"attributes":{"header":1},"insert":"\n"}]',
        '<h1>Hello</h1>'.PHP_EOL.'<p><br></p>'.PHP_EOL.'<p>This is the php quill <strong>parser</strong>!</p>'.PHP_EOL => '[{"insert":"Hello"},{"attributes":{"header":1},"insert":"\n"},{"insert":"\nThis is the php quill "},{"attributes":{"bold":true},"insert":"parser"},{"insert":"!\n"}]',
        '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/Ybq878PMe_U?showinfo=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>'.PHP_EOL => '[{"insert":{"video":"https://www.youtube.com/embed/Ybq878PMe_U?showinfo=0"}}]',
        '<p><img src="https://example.com/image.jpg" alt="" class="img-responsive img-fluid" />text</p>'.PHP_EOL => '[{"insert": {"image": "https://example.com/image.jpg"}},{"insert": "text\n"}]',
        '<p>before1</p>'.PHP_EOL.'<p><img src="https://example.com/image.jpg" alt="" class="img-responsive img-fluid" /></p>'.PHP_EOL.'<p>after</p>'.PHP_EOL => '[{"insert": "before1\n"},{"insert": {"image": "https://example.com/image.jpg"}},{"insert": "\nafter\n"}]',
        '<p>before2<img src="https://example.com/image.jpg" alt="" class="img-responsive img-fluid" />after</p>'.PHP_EOL => '[{"insert": "before2"},{"insert": {"image": "https://example.com/image.jpg"}},{"insert": "after\n"}]',
        '<ol>'.PHP_EOL.'<li>Title <strong>bold</strong></li>'.PHP_EOL.'</ol>'.PHP_EOL => '[{"insert":"Title "},{"attributes":{"bold":true},"insert":"bold"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<ol>'.PHP_EOL.'<li>List content</li>'.PHP_EOL.'</ol>'.PHP_EOL.'<p>afternewline</p>'.PHP_EOL => '[{"insert":"List content"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"afternewline\n"}]',
        '<ol>'.PHP_EOL.'<li><strong>bold</strong></li>'.PHP_EOL.'<li><em>italic</em></li>'.PHP_EOL.'<li><u>underline</u></li>'.PHP_EOL.'<li><del>strike</del></li>'.PHP_EOL.'</ol>'.PHP_EOL.'<p><br></p>'.PHP_EOL.'<p>normal Text</p>'.PHP_EOL => '{"ops":[{"attributes":{"bold":true},"insert":"bold"},{"attributes":{"list":"ordered"},"insert":"\n"},{"attributes":{"italic":true},"insert":"italic"},{"attributes":{"list":"ordered"},"insert":"\n"},{"attributes":{"underline":true},"insert":"underline"},{"attributes":{"list":"ordered"},"insert":"\n"},{"attributes":{"strike":true},"insert":"strike"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"\nnormal Text\n"}]}',
        '<ul>'.PHP_EOL.'<li><strong>foo</strong></li>'.PHP_EOL.'<li><em>bar</em></li>'.PHP_EOL.'</ul>'.PHP_EOL => '[{"attributes":{"bold":true},"insert":"foo"},{"attributes":{"list":"bullet"},"insert":"\n"},{"attributes":{"italic":true},"insert":"bar"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<ul>'.PHP_EOL.'<li>a <strong>b</strong> c</li>'.PHP_EOL.'</ul>'.PHP_EOL => '[{"insert":"a "},{"attributes":{"bold":true},"insert":"b"},{"insert":" c"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<ul>'.PHP_EOL.'<li>a <strong>b</strong> c</li>'.PHP_EOL.'<li><u>u</u></li>'.PHP_EOL.'<li><u>i</u>talic</li>'.PHP_EOL.'</ul>'.PHP_EOL.'<p>newline</p>'.PHP_EOL => '[{"insert":"a "},{"attributes":{"bold":true},"insert":"b"},{"insert":" c"},{"attributes":{"list":"bullet"},"insert":"\n"},{"attributes":{"underline":true},"insert":"u"},{"attributes":{"list":"bullet"},"insert":"\n"},{"attributes":{"underline":true},"insert":"i"},{"insert":"talic"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"newline\n"}]',
        '<ul>'.PHP_EOL.'<li><strong>a </strong>a</li>'.PHP_EOL.'<li>b</li>'.PHP_EOL.'</ul>'.PHP_EOL.'<p>paragraph</p>'.PHP_EOL.'<ul>'.PHP_EOL.'<li>c</li>'.PHP_EOL.'<li>d <strong>d</strong></li>'.PHP_EOL.'</ul>'.PHP_EOL => '[{"attributes":{"bold":true},"insert":"a "},{"insert":"a"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"b"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"paragraph\nc"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"d "},{"attributes":{"bold":true},"insert":"d"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<ol>'.PHP_EOL.'<li>item 1</li>'.PHP_EOL.'<li>item 2</li>'.PHP_EOL.'<li>item 3</li>'.PHP_EOL.'</ol>'.PHP_EOL => '[{"insert":"item 1"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"item 2"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"item 3"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<ul>'.PHP_EOL.'<li>item 1</li>'.PHP_EOL.'<li>item 2</li>'.PHP_EOL.'<li>item 3</li>'.PHP_EOL.'</ul>'.PHP_EOL => '[{"insert":"item 1"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"item 2"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"item 3"},{"attributes":{"list":"bullet"},"insert":"\n"}]',
        '<p>Text</p>'.PHP_EOL.'<ol>'.PHP_EOL.'<li>List Item 1</li>'.PHP_EOL.'</ol>'.PHP_EOL => '[{"insert":"Text\nList Item 1"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>Text</p>'.PHP_EOL.'<ol>'.PHP_EOL.'<li><strong>List Item 1</strong></li>'.PHP_EOL.'</ol>'.PHP_EOL => '[{"insert":"Text\n"},{"attributes":{"bold":true},"insert":"List Item 1"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>Text</p>'.PHP_EOL.'<ol>'.PHP_EOL.'<li><strong>List </strong>Item<strong> 1</strong></li>'.PHP_EOL.'</ol>'.PHP_EOL => '[{"insert":"Text\n"},{"attributes":{"bold":true},"insert":"List "},{"insert":"Item"},{"attributes":{"bold":true},"insert":" 1"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>Text</p>'.PHP_EOL.'<ol>'.PHP_EOL.'<li><strong>List </strong>Item<strong> 1</strong></li>'.PHP_EOL.'<li>Item 2</li>'.PHP_EOL.'</ol>'.PHP_EOL => '[{"insert":"Text\n"},{"attributes":{"bold":true},"insert":"List "},{"insert":"Item"},{"attributes":{"bold":true},"insert":" 1"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"Item 2"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<ol>'.PHP_EOL.'<li><strong>List </strong>Item<strong> 1</strong></li>'.PHP_EOL.'<li>Item 2</li>'.PHP_EOL.'</ol>'.PHP_EOL => '[{"attributes":{"bold":true},"insert":"List "},{"insert":"Item"},{"attributes":{"bold":true},"insert":" 1"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"Item 2"},{"attributes":{"list":"ordered"},"insert":"\n"}]',
        '<p>text</p>'.PHP_EOL.'<ul>'.PHP_EOL.'<li>A</li>'.PHP_EOL.'<li><strong>B</strong></li>'.PHP_EOL.'</ul>'.PHP_EOL.'<p>text2</p>'.PHP_EOL.'<ul>'.PHP_EOL.'<li><em>C</em></li>'.PHP_EOL.'<li>D</li>'.PHP_EOL.'</ul>'.PHP_EOL.'<ol>'.PHP_EOL.'<li>E</li>'.PHP_EOL.'<li>F</li>'.PHP_EOL.'</ol>'.PHP_EOL => '{"ops":[{"insert":"text\nA"},{"attributes":{"list":"bullet"},"insert":"\n"},{"attributes":{"bold":true},"insert":"B"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"text2\n"},{"attributes":{"italic":true},"insert":"C"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"D"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"E"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"F"},{"attributes":{"list":"ordered"},"insert":"\n"}]}',
        '<ul>'.PHP_EOL.'<li>A</li>'.PHP_EOL.'<li>B</li>'.PHP_EOL.'</ul>'.PHP_EOL.'<p>Paragraph with <strong>bold</strong></p>'.PHP_EOL.'<ol>'.PHP_EOL.'<li>Y1 <em>Y2</em> Y3</li>'.PHP_EOL.'<li>X1 X2 <strong>X3</strong></li>'.PHP_EOL.'</ol>'.PHP_EOL.'<p>morre <strong>text</strong></p>'.PHP_EOL => '[{"insert":"A"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"B"},{"attributes":{"list":"bullet"},"insert":"\n"},{"insert":"Paragraph with "},{"attributes":{"bold":true},"insert":"bold"},{"insert":"\nY1 "},{"attributes":{"italic":true},"insert":"Y2"},{"insert":" Y3"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"X1 X2 "},{"attributes":{"bold":true},"insert":"X3"},{"attributes":{"list":"ordered"},"insert":"\n"},{"insert":"morre "},{"attributes":{"bold":true},"insert":"text"},{"insert":"\n"}]',
        '<p>Lorem <span style="font-family: serif;">Ipsum</span> Dolor. <span style="font-family: monospace;">Sit</span> Amet.</p>'.PHP_EOL => '{"ops":[{"insert":"Lorem "},{"attributes":{"font":"serif"},"insert":"Ipsum"},{"insert":" Dolor. "},{"attributes":{"font":"monospace"},"insert":"Sit"},{"insert":" Amet.\n"}]}',
        '<h2></h2>'.PHP_EOL.'<h2>title</h2>'.PHP_EOL.'<p>text</p>'.PHP_EOL => '{"ops":[{"attributes":{"header":2},"insert":"\n"},{"insert":"title"},{"attributes":{"header":2},"insert":"\n"},{"insert":"text\n"}]}',
        '<blockquote></blockquote>'.PHP_EOL.'<blockquote>blockquote</blockquote>'.PHP_EOL.'<p>text</p>'.PHP_EOL => '{"ops":[{"attributes":{"blockquote":true},"insert":"\n"},{"insert":"blockquote"},{"attributes":{"blockquote":true},"insert":"\n"},{"insert":"text\n"}]}',
        '<p>lorem <sub>ipsum</sub> dolor <sup>sit</sup> amet</p>'.PHP_EOL => '{"ops":[{"insert":"lorem "},{"attributes":{"script":"sub"},"insert":"ipsum"},{"insert":" dolor "},{"attributes":{"script":"super"},"insert":"sit"},{"insert":" amet\n"}]}',
        '<p>Lorem</p>'.PHP_EOL.'<p style="text-align: center;">Ipsum</p>'.PHP_EOL.'<p style="text-align: right;">Dolor</p>'.PHP_EOL.'<p style="text-align: justify;">Sit Amet</p>'.PHP_EOL => '{"ops":[{"insert":"Lorem\nIpsum"},{"attributes":{"align":"center"},"insert":"\n"},{"insert":"Dolor"},{"attributes":{"align":"right"},"insert":"\n"},{"insert":"Sit Amet"},{"attributes":{"align":"justify"},"insert":"\n"}]}',
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
        $this->assertSame('<p>f</p>'.PHP_EOL.'<p>foo</p>'.PHP_EOL, $lexer->render());
        
        $debug = new Debug($lexer);
        $this->assertNotNull($debug->debugPrint());
    }
}

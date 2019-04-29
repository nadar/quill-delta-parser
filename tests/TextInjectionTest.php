<?php

namespace nadar\quill\tests;

use nadar\quill\Lexer;
use nadar\quill\listener\Mention;

class TextInjectionTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {"insert": "<script>", "attributes": {"blockquote": true}},
    {"insert": "<script>", "attributes": {"bold": true}},
    {"insert": "<script>", "attributes": {"color": "<script>"}},
    {"insert": "\n<script>"},
    {"insert": "\n", "attributes": {"header": 1}},
    {"insert": {"image": "<script>"}},
    {"insert": "\n"},
    {"insert": "<script>", "attributes": {"italic": true}},
    {"insert": "<script>", "attributes": {"link": "<script>"}},
    {"insert": {"mention": {"id": "1", "value": "<script>", "denotationChar": "@"}}},
    {"insert": "\n"},
    {"insert": "<script>", "attributes": {"strike": true}},
    {"insert": "\n"},
    {"insert": "<script>"},
    {"insert": "<script>", "attributes": {"underline": true}},
    {"insert": "\n"},
    {"insert": {"video": "<script>"}}
]
JSON;

    public $html = <<<'EOT'
<blockquote>&lt;script&gt;</blockquote>
<p>
<strong>&lt;script&gt;</strong>
<span style="color:&lt;script&gt;">&lt;script&gt;</span>
</p>
<h1>&lt;script&gt;</h1>
<p><img src="&lt;script&gt;" alt="" class="img-responsive img-fluid" /></p>
<p>
<em>&lt;script&gt;</em>
<a href="&lt;script&gt;" target="_blank">&lt;script&gt;</a>
&lt;script&gt;
</p>
<p><del>&lt;script&gt;</del></p>
<p>
&lt;script&gt;
<u>&lt;script&gt;</u>
</p>
<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="&lt;script&gt;" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
EOT;

    public function getLexer()
    {
        $lexer = new Lexer($this->json);
        $lexer->escapeInput = true;
        $lexer->registerListener(new Mention());

        return $lexer;
    }
}

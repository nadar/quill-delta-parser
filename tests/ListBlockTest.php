<?php

namespace nadar\quill\tests;

class ListBlockTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {"insert": "Before\n"},
    
    {"insert": "A 1"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    {"insert": "A 2", "attributes": {"bold": true}},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    {"insert": "A 3"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    
    {"insert": {"video": "https://www.youtube.com/embed/Ybq878PMe_U?showinfo=0"}},
    
    {"insert": "B 1"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    {"insert": "B "},
    {"insert": {"image": "https://example.com/image.jpg"}},
    {"insert": " 2"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    {"insert": "B 3"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    
    {"insert": "In between\n"},
    
    {"insert": "C 1"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    {"insert": "C 2"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    {"insert": "C 3"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    
    {"insert": {"image": "https://example.com/image.jpg"}},
    {"insert": "\n"},
    
    {"insert": "D 1"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    {"insert": "D 2"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    {"insert": "D 3"},
    {"attributes": {"list": "bullet"}, "insert": "\n"},
    
    {"insert": "After\n"}
]
JSON;

    public $html = <<<'EOT'
<p>Before</p>
<ul>
<li>A 1</li>
<li><strong>A 2</strong></li>
<li>A 3</li>
</ul>
<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/Ybq878PMe_U?showinfo=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
<ul>
<li>B 1</li>
<li>B <img src="https://example.com/image.jpg" alt="" class="img-responsive img-fluid" /> 2</li>
<li>B 3</li>
</ul>
<p>In between</p>
<ul>
<li>C 1</li>
<li>C 2</li>
<li>C 3</li>
</ul>
<p><img src="https://example.com/image.jpg" alt="" class="img-responsive img-fluid" /></p>
<ul>
<li>D 1</li>
<li>D 2</li>
<li>D 3</li>
</ul>
<p>After</p>
EOT;
}

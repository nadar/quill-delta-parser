<?php

namespace nadar\quill\tests;

/**
 * A video embed with inline attributes must still be rendered as video and not as its raw json.
 */
class VideoInlineAttributesTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops": [
    {"insert": "Before\n"},
    {"insert": {"video": "https://www.youtube.com/embed/abc"}, "attributes": {"bold": true, "link": "https://example.com"}},
    {"insert": "After\n"}
]}
JSON;

    public $html = <<<'EOT'
<p>Before</p>
<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/abc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
<p>After</p>
EOT;
}

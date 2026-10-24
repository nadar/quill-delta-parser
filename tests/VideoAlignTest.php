<?php

namespace nadar\quill\tests;

/**
 * An aligned video embed must still be rendered as video and not as its raw json.
 */
class VideoAlignTest extends DeltaTestCase
{
    public $json = <<<'JSON'
{"ops": [
    {"insert": "Before\n"},
    {"insert": {"video": "https://www.youtube.com/embed/abc"}, "attributes": {"align": "center"}},
    {"insert": "Centered"},
    {"insert": "\n", "attributes": {"align": "center"}}
]}
JSON;

    public $html = <<<'EOT'
<p>Before</p>
<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/abc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
<p style="text-align: center;">Centered</p>
EOT;
}

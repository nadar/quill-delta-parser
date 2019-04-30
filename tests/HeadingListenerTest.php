<?php

namespace nadar\quill\tests;

/**
 * Testing the Header Listener no prev element exists:
 * 
 * ```php
 * if (!$prev) {
 *     $prev = $pick->line;
 * } 
 * ```
 */
class HeadingListenerTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {
        "insert" : "header",
      "attributes": {
        "header": 1
      }
    }
]
JSON;

    public $html = '<h1>header</h1>';
}
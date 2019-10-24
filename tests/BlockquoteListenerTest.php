<?php

namespace nadar\quill\tests;

/**
 * Testing the Blockquote Listener no prev element exists:
 *
 * ```php
 * if (!$prev) {
 *     $prev = $pick->line;
 * }
 * ```
 */
class BlockquoteListenerTest extends DeltaTestCase
{
    public $json = <<<'JSON'
[
    {
        "insert" : "blockquote",
      "attributes": {
        "blockquote": true
      }
    }
]
JSON;

    public $html = '<blockquote>blockquote</blockquote>';
}

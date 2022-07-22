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
    public $json = [
        [
            'insert' => 'blockquote',
            'attributes' => [
                'blockquote' => true,
            ],
        ],
    ];

    public $html = '<blockquote>blockquote</blockquote>';
}

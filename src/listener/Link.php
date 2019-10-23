<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;
use nadar\quill\Lexer;

/**
 * Convert links into a inline elements.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Link extends InlineListener
{
    /**
     * @since 2.3.0
     */
    public $wrapper = '<a href="{link}" target="_blank">{text}</a>';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $link = $line->getAttribute('link');
        if ($link) {
            $this->updateInput($line, str_replace(['{link}', '{text}'], [$line->getLexer()->escape($link), $line->getInput()], $this->wrapper));
        }
    }
}

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
     * {@inheritDoc}
     */
    public function process(Line $line, Lexer $lexer=null)
    {
        $link = $line->getAttribute('link');
        if ($link) {
            $link = ($lexer->escapeInput) ? self::escape($link) : $link;
            $this->updateInput($line, '<a href="'.$link.'" target="_blank">'.$line->escapedInput().'</a>');
        }
    }
}

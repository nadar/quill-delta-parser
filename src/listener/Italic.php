<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;
use nadar\quill\Lexer;

/**
 * Convert Italic Inline elements.
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Italic extends InlineListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if ($line->getAttribute('italic')) {
            $this->updateInput($line, '<em>'.$line->input.'</em>');
        }
    }
}

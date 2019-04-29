<?php

namespace nadar\quill\listener;

use nadar\quill\Lexer;
use nadar\quill\Line;
use nadar\quill\InlineListener;

/**
 * Convert Bold attributes into tags.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Bold extends InlineListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line, Lexer $lexer=null)
    {
        if ($line->getAttribute('bold')) {
            $this->updateInput($line, '<strong>'.$line->escapedInput().'</strong>');
        }
    }
}

<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Lexer;
use nadar\quill\Line;

/**
 * Process strike elements
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Strike extends InlineListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line, Lexer $lexer=null)
    {
        if ($line->getAttribute('strike')) {
            $this->updateInput($line, '<del>'.$line->escapedInput().'</del>');
        }
    }
}

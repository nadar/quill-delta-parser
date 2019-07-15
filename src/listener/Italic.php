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
    const ATTRIBUTE_NAME = 'italic';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if ($line->getAttribute(self::ATTRIBUTE_NAME)) {
            $this->updateInput($line, '<em>'.$line->getInput().'</em>');
        }
    }
}

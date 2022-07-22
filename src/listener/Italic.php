<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

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
            $this->updateInput($line, '<em>'.$line->getInput().'</em>');
        }
    }
}

<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

/**
 * Process underline elements
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Underline extends InlineListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if ($line->getAttribute('underline')) {
            $this->updateInput($line, '<u>'.$line->input.'</u>');
        }
    }
}
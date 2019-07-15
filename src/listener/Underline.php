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
    const ATTRIBUTE_NAME = 'underline';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if ($line->getAttribute(self::ATTRIBUTE_NAME)) {
            $this->updateInput($line, '<u>'.$line->getInput().'</u>');
        }
    }
}

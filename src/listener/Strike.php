<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

/**
 * Process strike elements
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Strike extends InlineListener
{
    const ATTRIBUTE_NAME = 'strike';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if ($line->getAttribute(self::ATTRIBUTE_NAME)) {
            $this->updateInput($line, '<del>'.$line->getInput().'</del>');
        }
    }
}

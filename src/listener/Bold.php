<?php

namespace nadar\quill\listener;

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
    const ATTRIBUTE_NAME = 'bold';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if ($line->getAttribute(self::ATTRIBUTE_NAME)) {
            $this->updateInput($line, '<strong>'.$line->getInput().'</strong>');
        }
    }
}

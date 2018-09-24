<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\BlockListener;

/**
 * Convert header into heading elements.
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Heading extends BlockListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $heading = $line->getAttribute('header');
        if ($heading) {
            $prev = $line->previous();
            $prev->output = '<h'.$heading.'>'.$prev->input.'</h'.$heading.'>';
            $line->setDone();
            $prev->setDone();
        }
    }
}

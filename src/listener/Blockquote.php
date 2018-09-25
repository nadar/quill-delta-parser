<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\BlockListener;

/**
 * Convert Blockquote Elements
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Blockquote extends BlockListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $blockquote = $line->getAttribute('blockquote');
        if ($blockquote) {
            $prev = $line->previous();
            $prev->output = '<blockquote>'.$prev->input.'</blockquote>';
            $line->setDone();
            $prev->setDone();
        }
    }
}

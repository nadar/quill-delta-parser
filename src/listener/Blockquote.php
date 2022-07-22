<?php

namespace nadar\quill\listener;

use nadar\quill\BlockListener;
use nadar\quill\Lexer;
use nadar\quill\Line;

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
        if ($line->getAttribute('blockquote')) {
            $this->pick($line);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        $this->wrapElement('<blockquote>{__buffer__}</blockquote>');
    }
}

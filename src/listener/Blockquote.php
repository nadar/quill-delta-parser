<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\BlockListener;
use nadar\quill\Lexer;

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
        foreach ($this->picks() as $pick) {
            // get all
            $prev = $pick->line->previous(function (Line $line) {
                if (!$line->isInline()) {
                    return true;
                }
            });

            // if there is no previous element, we take the same line element.
            if (!$prev) {
                $prev = $pick->line;
            }

            $pick->line->output = '<blockquote>'.$prev->input . $pick->line->renderPrepend() . '</blockquote>';
            $prev->setDone();
        }
    }
}

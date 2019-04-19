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
            $this->pick($line, ['heading' => $heading]);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(\nadar\quill\Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            if (in_array($pick->heading, [1, 2, 3, 4, 5, 6], $strict=true) === false) {
                // prevent html injection in case the attribute is user input
                throw new \Exception('unknown heading level');
            }
            
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

            $pick->line->output = '<h'.$pick->heading.'>'.$prev->escapedInput() . $pick->line->renderPrepend() . '</h'.$pick->heading.'>';
            $prev->setDone();
        }
    }
}

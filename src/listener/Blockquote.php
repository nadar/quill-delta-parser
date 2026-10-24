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
        $picks = $this->picks();
        foreach ($picks as $i => $pick) {
            $first = $this->getFirstLine($pick);

            // while from first to the pick line and store content in buffer
            $buffer = null;
            $first->while(static function (&$index, Line $line) use (&$buffer, $pick, $first) {
                ++$index;
                $buffer .= $line->getInput();
                $line->setDone();
                if ($index == $pick->line->getIndex() || $first->getIndex() === $pick->line->getIndex()) {
                    return false;
                }
            });

            // consecutive blockquote lines are rendered into one blockquote, separated by line breaks
            $prev = $picks[$i - 1] ?? null;
            $next = $picks[$i + 1] ?? null;
            $isBlockStart = !$prev || $prev->line->getIndex() + 1 !== $first->getIndex();
            $isBlockEnd = !$next || $this->getFirstLine($next)->getIndex() !== $pick->line->getIndex() + 1;

            $output = $isBlockStart ? '<blockquote>' : '<br>';
            $output .= $buffer;
            if ($isBlockEnd) {
                $output .= '</blockquote>'.PHP_EOL;
            }

            $pick->line->output = $output;
            $pick->line->setDone();
        }
    }
}

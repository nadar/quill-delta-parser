<?php

namespace nadar\quill\listener;

use Exception;
use nadar\quill\Line;
use nadar\quill\BlockListener;
use nadar\quill\Lexer;

/**
 * Convert header into heading elements.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Heading extends BlockListener
{
    /**
     * @var array Supported header levels.
     * @since 1.2.0
     */
    public $levels = [1, 2, 3, 4, 5, 6];

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
     *
     * @throws Exception for unknown heading levels {@since 1.2.0}
     */
    public function render(Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            if (!in_array($pick->heading, $this->levels)) {
                // prevent html injection in case the attribute is user input
                throw new Exception('An unknown heading level "' . $pick->heading . '" has been detected.');
            }

            $first = $this->getFirstLine($pick);

            // while from first to the pick line and store content in buffer
            $buffer = null;
            $first->while(function (&$index, Line $line) use (&$buffer, $pick, $first) {
                $index++;
                $buffer .= $line->getInput();
                $line->setDone();
                // if the index of the picked lines is reached or the first element is the picked index.
                if ($index == $pick->line->getIndex() || $first->getIndex() == $pick->line->getIndex()) {
                    return false;
                }
            });

            $pick->line->output = '<h' . $pick->heading . '>' . $buffer . '</h' . $pick->heading . '>';
            $pick->line->setDone();
        }
    }
}

<?php

namespace nadar\quill\listener;

use Exception;
use nadar\quill\Line;
use nadar\quill\BlockListener;
use nadar\quill\Lexer;

/**
 * Convert align attributes into p tags with text-align css applied.
 *
 * @author Gaëtan Faugère <gaetan@fauge.re>
 * @since 2.4.0
 */
class Align extends BlockListener
{
    /**
     * @var array Supported alignments.
     */
    public $alignments = ['center', 'right', 'justify'];

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $alignment = $line->getAttribute('align');
        if ($alignment) {
            $this->pick($line, ['alignment' => $alignment]);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception for unknown alignment values
     */
    public function render(Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            if (!in_array($pick->alignment, $this->alignments)) {
                // prevent html injection in case the attribute is user input
                throw new Exception('An unknown alignment "' . $pick->alignment . '" has been detected.');
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

            $pick->line->output = '<p style="text-align: ' . $pick->alignment . ';">' . $buffer . '</p>';
            $pick->line->setDone();
        }
    }
}

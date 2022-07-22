<?php

namespace nadar\quill\listener;

use Exception;
use nadar\quill\BlockListener;
use nadar\quill\Lexer;
use nadar\quill\Line;

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
            if (!in_array($pick->optionValue('alignment'), $this->alignments)) {
                // prevent html injection in case the attribute is user input
                throw new Exception('An unknown alignment "' . $pick->optionValue('alignment') . '" has been detected.');
            }
        }

        $this->wrapElement('<p style="text-align: {alignment};">{__buffer__}</p>', ['alignment']);
    }
}

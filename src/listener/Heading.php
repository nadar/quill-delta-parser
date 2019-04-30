<?php

namespace nadar\quill\listener;

use Exception;
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
     * @since 1.2.0 Added exception
     * @throws Exception for unknown heading levels
     */
    public function render(\nadar\quill\Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            if (!in_array($pick->heading, $this->levels)) {
                // prevent html injection in case the attribute is user input
                throw new Exception('An unknown heading level "'.$pick->heading.'" has been detected.');
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

            $pick->line->output = '<h'.$pick->heading.'>'.$prev->getInput() . $pick->line->renderPrepend() . '</h'.$pick->heading.'>';
            $prev->setDone();
        }
    }
}

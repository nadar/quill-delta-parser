<?php

namespace nadar\quill\listener;

use Exception;
use nadar\quill\Line;
use nadar\quill\BlockListener;
use nadar\quill\Lexer;
use nadar\quill\options\AlignTrait;

/**
 * Convert header into heading elements.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Heading extends BlockListener
{
    use AlignTrait;

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
            $this->pick($line, ['heading' => $heading, 'align' => $this->getAlignOption($line)]);
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
        }
        
        $this->wrapElement('<h{heading}>{align}{__buffer__}{align}</h{heading}>', ['heading', 'align' => [$this, 'alignValue']]);
    }
}

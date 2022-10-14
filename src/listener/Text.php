<?php

namespace nadar\quill\listener;

use nadar\quill\BlockListener;
use nadar\quill\Lexer;
use nadar\quill\Line;

/**
 * Convert all the not done elements into paragraphs.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Text extends BlockListener
{
    /**
     * @var string
     */
    public const CLOSEP = '</p>'.PHP_EOL;

    /**
     * @var string
     */
    public const OPENP = '<p>';

    /**
     * @var string
     */
    public const LINEBREAK = '<br>';

    /**
     * {@inheritDoc}
     */
    public function priority(): int
    {
        return self::PRIORITY_GARBAGE_COLLECTOR;
    }

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if (!$line->isDone()) {
            $this->pick($line);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        $isOpen = false;
        foreach ($this->picks() as $pick) {
            if (!$pick->line->isDone() && !$pick->line->hasAttributes() && !$pick->line->isInline()) {
                $pick->line->setDone();

                $next = $pick->line->next();
                $prev = $pick->line->previous();

                $output = [];

                // if its close - we just open tag paragraph as we have a line here!
                if (!$isOpen) {
                    $isOpen = $this->output($output, self::OPENP, true);
                }

                // write the actuall content of the element into the output
                $output[] = $pick->line->isEmpty() ? self::LINEBREAK : $pick->line->renderPrepend() . $pick->line->getInput();

                // if its open and we have a next element, and the next element is not an inline, we close!
                if ($isOpen && ($next && !$next->isInline())) {
                    $isOpen = $this->output($output, self::CLOSEP, false);

                // if its open and we dont have a next element, its the end of the document! lets close this damn paragraph.
                } elseif ($isOpen && !$next) {
                    $isOpen = $this->output($output, self::CLOSEP, false);

                // its open, but the previous element was already an inline element, so maybe we should close and the next element
                // will take care of the "situation". But only if this current line also had an end new line element, otherwise
                // repeated inline elements will close
                } elseif ($isOpen && ($prev && $prev->isInline()) && $pick->line->hasEndNewline()) {
                    $isOpen = $this->output($output, self::CLOSEP, false);

                // If this element is empty we should maybe directly close and reopen this paragraph as it could be an empty line with
                // a next elmenet
                } elseif ($pick->line->isEmpty() && $next) {
                    $isOpen = $this->output($output, self::CLOSEP.self::OPENP, true);

                // if its open, and it had an end newline, lets close
                } elseif ($isOpen && $pick->line->hasEndNewline()) {
                    $isOpen = $this->output($output, self::CLOSEP, false);
                }

                // we have a next element and the next elmenet is inline and its not open, and the current elemnt is not an endNewline element
                if ($next && $next->isInline() && !$isOpen && !$pick->line->hasEndNewline()) {
                    $isOpen = $this->output($output, self::OPENP, true);
                }

                $pick->line->output = implode("", $output);
            }
        }
    }

    /**
     * Helper method simplify output writer.
     *
     * @param array<string> $output
     * @param string $tag
     * @param boolean $openState
     * @return boolean
     */
    protected function output(&$output, $tag, $openState)
    {
        $output[] = $tag;
        return $openState;
    }
}

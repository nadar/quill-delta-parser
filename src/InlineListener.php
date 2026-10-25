<?php

namespace nadar\quill;

use Exception;

/**
 * Inline listener.
 *
 * Inline listeners changes the $line->input value!
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
abstract class InlineListener extends Listener
{
    /**
     * {@inheritDoc}
     */
    public function type(): int
    {
        return self::TYPE_INLINE;
    }

    /**
     * A short hand method for handling inline elements.
     *
     * 1. change input value
     * 2. set as done, inline, and escaped
     * 3. Add to pick list, in order to process in render method
     *
     * @param Line $line
     * @param mixed $value
     * @return void
     */
    public function updateInput(Line $line, $value)
    {
        // the line has already been rendered as a block element (e.g. a video embed), inline formats can not be applied
        if ($line->output !== null) {
            return;
        }

        // we override the current element, and mark as done and mark as inline
        $line->setInput($value);
        $line->setDone();
        $line->setAsInline();
        $line->setAsEscaped();
        $this->pick($line);
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            $next = $pick->line->next(static function (Line $line) {
                return !$line->isInline();
            });

            if (!$next) {
                throw new Exception(sprintf("Unable to find a next element. Invalid DELTA on '%s'. Maybe your delta code does not end with a newline?", $pick->line->getInput()));
            }

            $next->addPrepend($pick->line->getInput(), $pick->line);
        }
    }
}

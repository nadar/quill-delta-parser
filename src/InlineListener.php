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
        // we override the current element, and mark as done and mark as inline
        $line->setInput($value);
        $line->setDone();
        $line->setAsInline();
        $line->setAsEscaped();
        $this->pick($line);
    }

    /**
     * Postpones the processing of this listener when the line contains an embed (json insert) which is not converted yet.
     *
     * Attribute listeners like bold or italic wrap the line input. When they run before the listener of an embed (e.g. a
     * mention), the embed would no longer be detected and its raw json is rendered instead. Therefore the attribute listener
     * is processed again for this line, once all listeners had the chance to convert the embed.
     *
     * @param Line $line
     * @return boolean Whether the processing has been postponed.
     */
    protected function deferUnprocessedEmbed(Line $line)
    {
        if (!$line->hasUnprocessedEmbed()) {
            return false;
        }

        $line->addDeferredListener($this);

        return true;
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

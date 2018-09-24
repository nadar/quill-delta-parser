<?php

namespace nadar\quill;

/**
 * Inline Listenere.
 *
 * Inline listeneres changes the $line->input value!
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
     * 2. set as done and inline
     * 3. Add to pick list, in order to process in render method
     *
     * @param Line $line
     * @param [type] $value
     * @return void
     */
    public function updateInput(Line $line, $value)
    {
        // we override the current element, and mark as done and mark as inline
        $line->input = $value;
        $line->setDone();
        $line->setAsInline();
        $this->pick($line);
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {

            $next = $pick->line->next(function(Line $line) {
                return !$line->getIsInline();
            });

            $next->addPrepend($pick->line->input);

        }
    }
}

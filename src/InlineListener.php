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
    public function type(): int
    {
        return self::TYPE_INLINE;
    }

    /**
     * A short hand method for handling inline elements.
     * 
     * 1. change input value
     * 2. set as done and inline
     * 3. preprend the value to the next element.
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

        // as inline elements might not appear, they are commonly part of a next line, so prepend
        // the current element input to the next element input
        $next = $line->next();
        $next->input = $line->input . $next->input;
    }
}

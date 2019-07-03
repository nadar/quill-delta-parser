<?php

namespace nadar\quill;

/**
 * Block Listener
 *
 * Block listeneres writes from $line->input into the $line->output.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
abstract class BlockListener extends Listener
{
    /**
     * {@inheritDoc}
     */
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }

    /**
     * Returns the first Line from a Pick. If the Pick is the first Line, it will return it's own pick
     * This is done because blockItems can consist of multiple inline items
     *
     * @param Pick $pick
     * @return Line
     * @since 1.3.2
     */
    protected function getFirstLine(Pick $pick) : Line
    {
        $first = $pick->line;

        $pick->line->while(
            function (&$index, Line $line) use ($pick, &$first) {
                $index--;
                // its the same line as the start.. skip this one as its by default included in while operations
                if ($line === $pick->line) {
                    return true;
                } elseif (($line->hasEndNewline() || $line->hasNewline() || $line->isJsonInsert())) {
                    return false;
                }

                // assign the line to $first
                $first = $line;
                return true;
            }
        );
        return $first;
    }
}

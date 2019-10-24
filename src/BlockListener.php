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
     * render all lines using a single outer wrapper
     * 
     * @param  string $wrapper html snippet using `{_buffer}` for the placement of the lines
     * @param  array  $options optional, pass the search patterns for options from the lines
     *                         note pass them in the same order they are applied on the Pick object
     * @since 2.4.0
     */
    protected function renderWithSimpleWarpper($wrapper, array $options=[])
    {
        foreach ($this->picks() as $pick) {
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

            $search    = $options;
            $replace   = $pick->getOptions();
            $search[]  = '{_buffer}';
            $replace[] = $buffer;
            
            $pick->line->output = str_replace($search, $replace, $wrapper);
            $pick->line->setDone();
        }
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
                } elseif (($line->hasEndNewline() || $line->hasNewline() || ($line->isJsonInsert() && !$line->isInline()))) {
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

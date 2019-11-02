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
     * Generate a rendered output from the current picks with a custom Wrapper Template.
     *
     * The main purpose of this method is so simplify the process of working with block elements. The method
     * will take the `picks()` of the current Listener Object to generate the first and list line of the block
     * and render its content inbetween.
     *
     * Example:
     *
     * ```php
     * public function render(Lexer $lexer)
     * {
     *     $this->wrapElement('<blockquote>{_buffer}</blockquote>');
     * }
     * ```
     *
     * The above method will no take the picked element and render a `blockquote` tag with its content stored in `{_buffer}`.
     * 
     * Assuming you pass options to the picked element during the `process()` stage you might use those options as variable
     * in brackes and passed to `$options` param:
     * 
     * ```php
     * $this->wrapElement('<h{heading}>{_buffer}</h{heading}>', ['heading']);
     * ```
     *
     * The above example assumes the heading option is stored during the process stage: `$this->pick($line, ['heading' => $heading]);`
     *
     * @param string $wrapper html snippet using `{__buffer__}` for the placement of the lines
     * @param array $options optional, pass the names of options from the lines you want to search & replace
     * e.g. using ['key'] will replace `{key}` in the $wrapper with Pick->$key.
     * @since 2.4.0
     */
    protected function wrapElement($wrapper, array $options = [])
    {
        $search = ['{__buffer__}'];
        foreach ($options as $name) {
            $search[] = '{'.$name.'}';
        }
        
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

            $replace = [$buffer];
            foreach ($options as $name) {
                $replace[] = $pick->$name;
            }
            
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

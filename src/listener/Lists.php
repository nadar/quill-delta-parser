<?php

namespace nadar\quill\listener;

use Exception;
use nadar\quill\Line;
use nadar\quill\Lexer;
use nadar\quill\BlockListener;
use nadar\quill\Pick;

/**
 * Convert List elements (ul, ol) into Block element.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Lists extends BlockListener
{
    const ATTRIBUTE_LIST = 'list';

    const LIST_TYPE_BULLET = 'bullet';

    const LIST_TYPE_ORDERED = 'ordered';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $listType = $line->getAttribute(self::ATTRIBUTE_LIST);
        if ($listType) {
            $this->pick($line, ['type' => $listType]);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        $isOpen = false;
        $listTag = null;
        foreach ($this->picks() as $pick) {
            $first = $this->getFirstLine($pick);

            // while from first to the pick line and store content in buffer
            $buffer = null;
            $first->while(function (&$index, Line $line) use (&$buffer, $pick) {
                $index++;
                $buffer.= $line->getInput();
                $line->setDone();
                if ($index == $pick->line->getIndex()) {
                    return false;
                }
            });

            // defines whether this attribute list element is the last one of a list serie.
            $isLast = false;

            // go to the next element with endlinew and check if it contains a list type until then
            $hasNextInside = false;
            $pick->line->whileNext(function (Line $line) use (&$hasNextInside) {
                // we found the next list elemnt, stop thie while loop
                if ($line->getAttribute(self::ATTRIBUTE_LIST)) {
                    return false;
                }
                // if one of those new lines contains a endnew line or newline or is block level store this information
                if ($line->hasEndNewline() || $line->hasNewline() || ($line->isJsonInsert() && !$line->isInline())) {
                    $hasNextInside = true;
                }
            });

            // There was a newline element until next list element, so end of list has reached.
            if ($hasNextInside) {
                $isLast = true;
            }

            $output = null;

            // this makes sure that when two list types are after each other (OL and UL)
            // the previous will be closed so the new one will open
            if ($isOpen && $listTag && $listTag !== $this->getListAttribute($pick)) {
                $output .= '</'.$listTag.'>';
                $isOpen = false;
            }

            // create the opening OL/UL tag
            // opening tag process has been simplified, see https://github.com/nadar/quill-delta-parser/pull/33
            // and https://github.com/nadar/quill-delta-parser/issues/30
            if (!$isOpen) {
                $output .= '<'.$this->getListAttribute($pick).'>';
                $isOpen = true;
            }

            // write the li element.
            $output.= '<li>' . $buffer .'</li>';

            // close the opening OL/UL tag if:
            //   a. its the last element and the tag is opened.
            //   b. or its the last element in the picked list.
            if (($isOpen && $isLast) || ($isOpen && $pick->isLast())) {
                $output .= '</'.$this->getListAttribute($pick).'>';
                $isOpen = false;
            }

            // store the last list type into a variable to determine if type switches
            $listTag = $this->getListAttribute($pick);

            $pick->line->output = $output;
            $pick->line->setDone();
        }
    }

    /**
     * Get the html tag for the given value.
     *
     * @param Pick $pick
     * @return string
     * @throws Exception for unknown list types {@since 1.2.0}
     */
    protected function getListAttribute(Pick $pick)
    {
        if ($pick->type == self::LIST_TYPE_ORDERED) {
            return 'ol';
        }

        if ($pick->type == self::LIST_TYPE_BULLET) {
            return 'ul';
        }

        // prevent html injection in case the attribute is user input
        throw new Exception('The provided list type "'.$pick->type.'" is not a known list type (ordered or bullet).');
    }
}

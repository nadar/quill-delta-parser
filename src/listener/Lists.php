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
        foreach ($this->picks() as $pick) {
            // get the first element within this list <li>
            $first = $pick->line->previous(function (Line $line) {
                if ($line->isFirst() || $line->hasNewline()) {
                    return true;
                }
            });

            // while from first to pick line and store content in buffer
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

            // find the next element which is NOT empty.
            $next = $pick->line->next(function (Line $line) {
                return !$line->isEmpty();
            });

            // if there is a next element and this element has a new line, this is the last element.
            if ($next && $next->hasNewline()) {
                $isLast = true;
            }

            $output = null;

            // create the opining OL/UL tag if:
            //  a. its not already open AND $isLast is false (which means not the last element)
            //  b. or its the first the pick inside the picked elements list https://github.com/nadar/quill-delta-parser/issues/8
            if ((!$isOpen && !$isLast) || (!$isOpen && $pick->isFirst())) {
                $output .= '<'.$this->getListAttribute($pick).'>';
                $isOpen = true;
            }

            // write the li element.
            $output.= '<li>' . $buffer .'</li>';
            
            // close the opening OL/UL tag if:
            //   a. its the last element and the tag is opened.
            //   b. or its the last element in the picked list.
            if (($isLast && $isOpen) || ($isOpen && $pick->isLast())) {
                $output .= '</'.$this->getListAttribute($pick).'>';
                $isOpen = false;
            }

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

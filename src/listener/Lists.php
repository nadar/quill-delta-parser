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
        $lists = [];

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

            // find out if last element of series of lists
            // if a. is no next element
            // or b. next element "has new line"
            $isLast = false;
            if (!$pick->line->next() || $pick->line->next(function (Line $line) {
                return !$line->isInline();
            })->hasNewline()) {
                $isLast = true;
            }

            // write the li element.
            $output = null;
            if (!$isOpen && !$isLast) {
                $output .= '<'.$this->getListAttribute($pick).'>';
                $isOpen = true;
            }
            $output.= '<li>' . $buffer .'</li>';
            
            if ($isLast && $isOpen) {
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
     * @since 1.2.0 Added exception
     * @param Pick $pick
     * @return string
     * @throws Exception for unknown list types
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

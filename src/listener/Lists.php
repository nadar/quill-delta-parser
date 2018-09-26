<?php

namespace nadar\quill\listener;

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
                $buffer.= $line->input;
                $line->setDone();
                if ($index == $pick->line->getIndex()) {
                    return false;
                }
            });

            // find out if last element of series of lists
            // if a. is no next element
            // or b. next element "has new line"
            $isLast = false;
            if (!$pick->line->next() || $pick->line->next()->hasNewline()) {
                $isLast = true;
            }

            // write the li element.
            $output = null;
            if (!$isOpen && !$isLast) {
                $output .= '<'.$this->getListAttribute($pick).'>';
            }
            $output.= '<li>' . $buffer .'</li>';
            if ($isLast) {
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
     */
    protected function getListAttribute(Pick $pick)
    {
        if ($pick->type == 'ordered') {
            return 'ol';
        }

        return 'ul';
    }
}

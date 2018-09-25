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
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $listType = $line->getAttribute('list');
        if ($listType) {
            /*
            $prev = $line->previous(); // the value for the <li>
            $prevPrev = $prev->previous();
            if ($prevPrev) {
                $prevPrevType = $prevPrev->getAttribute('list'); // does the element before the element has also a list?
            } else {
                $prevPrevType = false;
            }
            */
            $this->pick($line, ['type' => $listType]);
            $line->setDone();
        }
    }

    public function render(Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            
            // get the first element within this list <li>
            $first = $pick->line->previous(function(Line $line) {
                if ($line->isFirst() || $line->hasEndNewline()) {
                    return true;
                }
            });
            

            // while from first to pick line and store content in buffer
            $buffer = null;
            $first->while(function(&$index, Line $line) use (&$buffer, $pick) {
                $index++;
                $buffer.= $line->input;
                $line->setDone();
                if ($index == $pick->line->getIndex()) {
                    return false;
                }
            });

            if ($pick->isFirst()) {
                $pick->line->output = '<'.$this->getListAttribute($pick).'><li>' . $buffer .'</li>';
            } elseif ($pick->isLast()) {
                $pick->line->output = '<li>' . $buffer .'</li></'.$this->getListAttribute($pick).'>';
            } else {
                $pick->line->output = '<li>' . $buffer .'</li>';
            }
            
            $pick->line->setDone();

            // assign buffer to current pick lines output and mark as done.
        }
    }

    protected function getListAttribute(Pick $pick)
    {
        if ($pick->type == 'ordered') {
            return 'ol';
        }

        return 'ul';
    }

    /**
     * {@inheritDoc}
     */
    /*
    public function render(Lexer $lexer)
    {
        // go back to the first line or last line which ends with newline, this is the full content for the current list
        $isFirst = false;
        $list = [];
        $lastId = 0;
        foreach ($this->picks() as $pick) {
            if ($pick->isFirst) {
                $lastId = $pick->line->getIndex();
            }

            $list[$lastId][] = $pick->line;
        }

        foreach ($list as $index => $items) {
            $content = '<ul>';
            foreach ($items as $item) {
                $content.= '<li>'.$item->input.'</li>';
            }

            $lexer->getLine($index)->output = $content . '</ul>';
        }
    }
    */
}

<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\Lexer;
use nadar\quill\BlockListener;

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
            $prev = $line->previous(); // the value for the <li>
            $prevPrev = $prev->previous();
            if ($prevPrev) {
                $prevPrevType = $prevPrev->getAttribute('list'); // does the element before the element has also a list?
            } else {
                $prevPrevType = false;
            }

            $this->pick($prev, ['isFirst' => (bool) !$prevPrevType, 'type' => $listType]);
            $prev->setDone();
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
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
}

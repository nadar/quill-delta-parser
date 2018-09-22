<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\Lexer;
use nadar\quill\BlockListener;

class Lists extends BlockListener
{
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

            $this->pick($prev, ['isFirst' => (bool) !$prevPrevType]);
            $prev->setDone();
            $line->setDone();
        }
    }

    public function render(Lexer $lexer)
    {
        $isFirst = false;
        $list = [];
        $lastId = 0;
        foreach ($this->picks() as $pick) {
            if ($pick->isFirst) {
                $lastId = $pick->line->row;
            }

            $list[$lastId][] = $pick->line;
        }

        foreach ($list as $row => $items) {
            $content = '<ul>';
            foreach ($items as $item) {
                $content.= '<li>'.$item->input.'</li>';
            }

            $lexer->getLine($row)->output = $content . '</ul>';
        }
    }

    /*
    public function render(Parser $parser)
    {
        $content = null;
        $first = null;

        foreach ($this->getBag() as $delta) {

            //$delta->debugPrint('lists');

            if (!$first) {
                $first = $delta;
            } else {
                $delta->remove();
            }

            if (!$delta->getAttribute('list')) {
                $content.= '<li>'.$delta->getInsert() .'</li>';
            }
        }

        if ($first) {
            $first->setInsert('<ul>'.$content.'</ul>');
            $first->setDone();
        }
    }
    */
}

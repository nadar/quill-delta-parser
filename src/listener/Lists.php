<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;
use nadar\quill\Line;
use nadar\quill\Lexer;

class Lists extends Listener
{
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }
    
    public function process(Line $line)
    {
        $listType = $line->getAttribute('list');
        if ($listType) {
            $prev = $line->getPreviousLine(); // the value for the <li>
            $prevPrev = $prev->getPreviousLine()->getAttribute('list'); // does the element before the element has also a list?
            
            $this->pick($prev, ['isFirst' => (bool) !$prevPrev]);
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
<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;

class Lists extends Listener
{
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }
    
    public function process(Delta $delta)
    {
        $list = $delta->getAttribute('list'); // "bullet"
        if ($list) {
            $this->addToBag($delta);
            $this->addToBag($delta->getPreviousDelta());
        }
    }

    public function render(Parser $parser)
    {
        $content = null;
        $first = null;
        foreach ($this->getBag() as $delta) {
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
        }
    }
}
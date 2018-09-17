<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;

class Heading extends Listener
{   
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }
    
    public function process(Delta $delta)
    {
        $header = $delta->getAttribute('header');
        if ($header) {
            $this->addToBag($delta->getPreviousDelta());
            $this->addToBag($delta);
        }
    }

    public function render(Parser $parser)
    {
        if (empty($this->getBag())) {
            return;
        }
        list ($prev, $original) = $this->getBag();
        $header = $original->getAttribute('header');
        $original->setInsert('<h'.$header.'>'.$prev->getInsert().'</h'.$header.'>');
        $prev->remove();
    }
}
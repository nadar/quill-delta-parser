<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;

class Heading extends Listener
{
    public function priority(): int
    {
        return self::PRIORITY_EARLY_BIRD;
    }
    
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }
    
    public function process(Delta $delta)
    {
        $header = $delta->getAttribute('header');
        if ($header) {
            $delta->getParser()->writeBuffer('<h'.$header.'>'.$delta->getPreviousDelta()->getInsert().'</h'.$header.'>');
            $delta->getPreviousDelta()->setDone();
            $delta->setDone();
        }
        
    }

    public function render(Parser $parser)
    {
        
    }
}
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
            $prev = $delta->getPreviousDelta();
            
            $delta->debugPrint('heading');
            $prev->debugPrint('heading prev');
            
            $prev->setInsert('<h'.$header.'>'.$prev->getInsert().'</h'.$header.'>');

            $prev->setDone();
            $delta->setDone();
        }
    }

}
<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;


class Heading extends Listener
{
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }
    
    public function render(Delta $delta)
    {
        $header = $delta->getAttribute('header');
        if ($header) {
            $delta->getParser()->writeBuffer('<h'.$header.'>'.$delta->getPreviousDelta()->getInsert().'</h'.$header.'>');
            $delta->getPreviousDelta()->setDone();
            $delta->setDone();
        }
        
    }
}
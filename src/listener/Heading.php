<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;
use nadar\quill\Line;

class Heading extends Listener
{
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }
    
    public function process(Line $line)
    {
        $heading = $line->getAttribute('header');
        if ($heading) {
            $prev = $line->previous();
            $prev->output = '<h'.$heading.'>'.$prev->input.'</h'.$heading.'>';
            $line->setDone();
            $prev->setDone();
        }
    }
}

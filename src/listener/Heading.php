<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\BlockListener;

class Heading extends BlockListener
{
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

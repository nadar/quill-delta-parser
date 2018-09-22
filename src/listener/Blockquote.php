<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\BlockListener;

class Blockquote extends BlockListener
{
    public function process(Line $line)
    {
        $blockquote = $line->getAttribute('blockquote');
        if ($blockquote) {
            $prev = $line->previous();
            $prev->output = '<blockquote>'.$prev->input.'</blockquote>';
            $line->setDone();
            $prev->setDone();
        }
    }
}

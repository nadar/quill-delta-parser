<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Line;

class Blockquote extends Listener
{
    // blockquote
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }
    
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

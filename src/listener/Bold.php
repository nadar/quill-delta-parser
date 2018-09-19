<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;
use nadar\quill\Line;

class Bold extends Listener
{
    public function type(): int
    {
        return self::TYPE_INLINE;
    }

    public function process(Line $line)
    {
        if ($line->getAttribute('bold')) {
            $line->input = '<strong>'.$line->input.'</strong>';

        }
    }
}
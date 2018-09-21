<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;
use nadar\quill\Line;

class Italic extends Listener
{
    public function type(): int
    {
        return self::TYPE_INLINE;
    }

    public function process(Line $line)
    {
        if ($line->getAttribute('italic')) {
            $next = $line->next();
            $next->input = '<i>'.$line->input.'</i>' . $next->input;
            $line->setDone();
            $line->isInline = true;
        }
    }
}

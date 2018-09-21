<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;
use nadar\quill\Line;

class Link extends Listener
{
    public function type(): int
    {
        return self::TYPE_INLINE;
    }

    public function process(Line $line)
    {
        $link = $line->getAttribute('link');
        if ($line->getAttribute('link')) {
            $next = $line->next();
            $next->input = '<a href="'.$link.'">'.$line->input.'</a>' . $next->input;
            $line->setDone();
            $line->isInline = true;
        }
    }
}

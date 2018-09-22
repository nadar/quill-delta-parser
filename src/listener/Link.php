<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;

class Link extends InlineListener
{
    public function process(Line $line)
    {
        $link = $line->getAttribute('link');
        if ($link) {
            $this->updateInput($line, '<a href="'.$link.'">'.$line->input.'</a>');
        }
    }
}

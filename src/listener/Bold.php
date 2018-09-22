<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;

class Bold extends InlineListener
{
    public function process(Line $line)
    {
        if ($line->getAttribute('bold')) {
            $this->updateInput($line, '<strong>' . $line->input . '</strong>');
        }
    }
}

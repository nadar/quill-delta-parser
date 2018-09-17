<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;

class Italic extends Listener
{
    public function type(): int
    {
        return self::TYPE_INLINE;
    }

    public function process(Delta $delta)
    {
        if ($delta->getAttribute('italic')) {
            $delta->setInsert('<em>'.$delta->getInsert().'</em>');
        }
    }
}
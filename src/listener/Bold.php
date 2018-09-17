<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;
use nadar\quill\Parser;

class Bold extends Listener
{
    public function type(): int
    {
        return self::TYPE_INLINE;
    }

    public function process(Delta $delta)
    {
        if ($delta->getAttribute('bold')) {
            $delta->setInsert('<strong>'.$delta->getInsert().'</strong>');
        }
    }
}
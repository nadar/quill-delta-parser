<?php

namespace nadar\quill\listener;

use nadar\quill\Listener;
use nadar\quill\Delta;

class Lists extends Listener
{
    public function priority(): int
    {
        return self::PRIORITY_EARLY_BIRD;
    }
    
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }
    
    public function render(Delta $delta)
    {
        $list = $delta->getAttribute('list'); // "bullet"
        if ($list) {
            $item = $delta->getPreviousDelta();
        }
    }
}
<?php

namespace nadar\quill;

abstract class Listener
{  
    const TYPE_INLINE = 1;

    const TYPE_BLOCK = 2;
    
    const PRIORITY_EARLY_BIRD = 1;

    const PRIORITY_CASUAL = 2;

    const PRIORITY_GARBAGE_COLLECTOR = 3;

    public function priority(): int
    {
        return self::PRIORITY_CASUAL;
    }
    
    abstract public function type(): int;
    abstract public function render(Delta $delta);
}
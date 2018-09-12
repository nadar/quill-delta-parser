<?php

namespace nadar\quill;

abstract class Listener
{  
    const TYPE_INLINE = 1;

    const TYPE_BLOCK = 2;
    
    abstract public function type(): int;
    abstract public function render(Delta $delta);
}
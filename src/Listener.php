<?php

namespace nadar\quill;

abstract class Listener
{  
    const TYPE_INLINE = 1;

    const TYPE_BLOCK = 2;
    
    const PRIORITY_EARLY_BIRD = 1;

    const PRIORITY_CASUAL = 2;

    /**
     * This type of priorioty is generally used when the Listener checks whether a Delta `isDone()` already. Therefore it is
     * used to process "not done" deltas.
     */
    const PRIORITY_GARBAGE_COLLECTOR = 3;

    public function priority(): int
    {
        return self::PRIORITY_CASUAL;
    }

    private $_bag = [];

    public function addToBag(Delta $delta, $markAsDone = true)
    {
        $this->_bag[] = $delta;
        if ($markAsDone) {
            $delta->isDone();
        }
    }

    public function getBag()
    {
        return $this->_bag;
    }

    abstract public function type(): int;
    abstract public function process(Delta $delta);
    abstract public function render(Parser $parser);
    
}
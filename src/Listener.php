<?php

namespace nadar\quill;

abstract class Listener
{  
    const TYPE_INLINE = 1;

    const TYPE_BLOCK = 2;
    
    const PRIORITY_EARLY_BIRD = 1;

    /**
     * This type of priorioty is generally used when the Listener checks whether a Delta `isDone()` already. Therefore it is
     * used to process "not done" deltas.
     */
    const PRIORITY_GARBAGE_COLLECTOR = 3;

    public function priority(): int
    {
        return self::PRIORITY_EARLY_BIRD;
    }

    private $_bag = [];

    public function addToBag(Delta $delta, $setAsDone = true)
    {
        $this->_bag[] = $delta;
        /*
        if ($group) {
            $this->_bag[$group][] = $delta;
        } else {
            $this->_bag[] = $delta;
        }
        */
        if ($setAsDone) {
            $delta->setDone();
        }
        
    }

    public function getBag()
    {
        return $this->_bag;
    }

    /**
     * The render method is only triggered for: TYPE_BLOCK
     */
    public function render(Parser $parser)
    {

    }

    abstract public function type(): int;
    abstract public function process(Delta $delta);
    
}
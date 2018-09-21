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

    protected $_picks = [];

    public function pick(Line $line, array $options = [])
    {
        $line->setPicked();
        $this->_picks[] = new Pick($line, $options);
    }

    public function picks()
    {
        return $this->_picks;
    }

    public function render(Lexer $lexer)
    {
    }

    abstract public function type(): int;
    abstract public function process(Line $line);
}

<?php

namespace nadar\quill;

/**
 * Pick Object holds the line which is picked with optional data.
 *
 * @since 1.0.0
 * @author  Basil Suter <basil@nadar.io>
 */
class Pick
{
    /**
     * @var array An array with options which can be read trough magical getter
     */
    public $options = [];

    /**
     * @var Line
     */
    public $line;

    protected $index;

    protected $listener;
    
    /**
     * Create new line pick
     *
     * @param Line $line
     * @param array $options
     */
    public function __construct(Line $line, array $options = [], $index, Listener $listener)
    {
        $this->line = $line;
        $this->options = $options;
        $this->index = $index;
        $this->listener = $listener;
    }

    /**
     * @param mixed $name
     */
    public function __get($name)
    {
        return $this->options[$name];
    }

    public function isFirst()
    {
        return $this->index == 0;
    }

    public function isLast()
    {
        return (count($this->listener->picks()) - 1) == $this->index;
    }
}

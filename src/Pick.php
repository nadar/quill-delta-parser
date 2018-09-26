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
     * @var Line
     */
    public $line;

    /**
     * @var array An array with options which can be read trough magical getter
     */
    protected $options = [];

    /**
     * @var integer Contains the current index number for this pick.
     */
    protected $index;

    /**
     * @var Listener The listener object the picke was made from.
     */
    protected $listener;
    
    /**
     * Create new line pick
     *
     * @param Listener $listener
     * @param Line $line
     * @param array $options
     * @param integer $index Index of picks, starts with 0
     */
    public function __construct(Listener $listener, Line $line, array $options = [], $index)
    {
        $this->listener = $listener;
        $this->line = $line;
        $this->options = $options;
        $this->index = $index;
    }

    /**
     * @param mixed $name
     */
    public function __get($name)
    {
        return $this->options[$name];
    }

    /**
     * Whether current pick is the first pick inside this listenere.
     *
     * @return boolean
     */
    public function isFirst()
    {
        return $this->index == 0;
    }

    /**
     * Whether current pick is the last pick inside the list of picks.
     *
     * @return boolean
     */
    public function isLast()
    {
        return (count($this->listener->picks()) - 1) == $this->index;
    }
}

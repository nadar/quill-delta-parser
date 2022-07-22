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
    public function __construct(Listener $listener, Line $line, array $options, $index)
    {
        $this->listener = $listener;
        $this->line = $line;
        $this->options = $options;
        $this->index = $index;
    }

    /**
     * @param mixed $name
     * @deprecated Deprecated in 3.1 will be removed in 4.0 use `optionValue($name)` instead.
     */
    public function __get($name)
    {
        trigger_error("Deprecated in 3.1 will be removed in 4.0 use `optionValue('$name')` instead.", E_USER_NOTICE);
        return array_key_exists($name, $this->options) ? $this->options[$name] : null;
    }

    /**
     * Return the value of an option if available
     *
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     * @since 3.1.0
     */
    public function optionValue($name, $defaultValue = null)
    {
        return array_key_exists($name, $this->options) ? $this->options[$name] : $defaultValue;
    }

    /**
     * Whether current pick is the first pick inside the list of picks.
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

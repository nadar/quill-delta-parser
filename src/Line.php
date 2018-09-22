<?php

namespace nadar\quill;

/**
 * Line Object.
 * 
 * A line object represents a line from the delta input. Lines are splited in the lexer object.
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Line
{
    const STATUS_CLEAN = 1;
    const STATUS_PICKED = 2;
    const STATUS_DONE = 3;
    public $row;
    
    public $attributes = [];
    public $lexer;
    
    public $input;
    public $output;

    public $status = 1;
    public $isInline = false;

    /**
     * Undocumented function
     *
     * @param [type] $row
     * @param [type] $value
     * @param array $attributes
     * @param Lexer $lexer
     */
    public function __construct($row, $value, array $attributes, Lexer $lexer)
    {
        $this->row = $row;
        $this->input = $value;
        $this->attributes = $attributes;
        $this->lexer = $lexer;
    }

    public function hasAttribute()
    {
        return !empty($this->attributes);
    }

    public function getAttribute($name)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : false;
    }

    public function next()
    {
        return $this->lexer->getLine($this->row + 1);
    }

    public function previous()
    {
        return $this->lexer->getLine($this->row - 1);
    }

    public function setAsInline()
    {
        $this->isInline = true;
    }

    public function getIsInline()
    {
        return $this->isInline;
    }

    public function setPicked()
    {
        $this->status = self::STATUS_PICKED;
    }

    public function isPicked()
    {
        return $this->status == self::STATUS_PICKED;
    }

    public function setDone()
    {
        $this->status = self::STATUS_DONE;
    }

    public function isDone()
    {
        return $this->status == self::STATUS_DONE;
    }

    public function isEmpty()
    {
        return empty($this->input);
    }
}

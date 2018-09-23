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
    
    public $prepend;
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

    /**
     * Get the next element.
     * 
     * If a closure is provided you can define a condition of whether next element should be taken or not.
     * 
     * For example you can iterate to the next element which is not empty:
     * 
     * ```php
     * $nextNotEmpty = $line->next(function(Line $line) {
     *     return !$line->isEmpty();
     * });
     * ```
     * 
     * if true is returned this line will be assigned.
     */
    public function next($fn = null)
    {
        if ($fn === null) {
            return $this->lexer->getLine($this->row + 1);
        }

        $next = true;
        $i = 1;
        while ($next) {
            $elmn = $this->lexer->getLine($this->row + $i);
            // no next element found
            if (!$elmn) {
                return false;
            }
            // fn match return current element.
            if (call_user_func($fn, $elmn)) {
                return $elmn;
            }
            // update counter for rows
            $i++;
        }
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
        return $this->input == '' && $this->prepend == '';
    }

    /**
     * Some plugins have a json as insert value, in order to detected such values
     * you can use this method.
     * 
     * This will check if the given insert string is a json. In order to deocde the
     * json into a php array afterwards, you can use getArrayInsert();
     * 
     * @return boolean Whether current insert string is a json or not.
     */
    public function isJsonInsert()
    {
        return Lexer::isJson($this->input);
    }

    /**
     * Returns the insert json as array.
     *
     * @return array
     */
    public function getArrayInsert()
    {
        return Lexer::decodeJson($this->input);
    }
}

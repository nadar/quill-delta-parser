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
    /**
     * @var integer The status of a line which is not picked or done, which is default.
     */
    const STATUS_CLEAN = 1;

    /**
     * @var integer The status of the line if its picked by a listenere.
     */
    const STATUS_PICKED = 2;

    /**
     * @var integer The status of the line if some of the listeneres marked this line as done.
     */
    const STATUS_DONE = 3;

    /**
     * @var integer Holds the current status of the line.
     */
    protected $status = 1;

    /**
     * @var integer The ID/Index/Row of the line
     */
    protected $index;
    
    /**
     * @var array An array with all attributes which are assigned to this lines. attribute can be inline markers like
     * bold, italic, links and so on.
     */
    protected $attributes = [];

    /**
     * @var Lexer The lexer object in order to access other lines and elements.
     */
    protected $lexer;
    
    /**
     * @var array An array with values which can be prependend to the actuall input string. This is mainly used if inline
     * elements are passed to the next "not" inline element.
     */
    public $prepend = [];

    /**
     * @var string The input string which is assigned from the line parser. This is the actual content of the line itself!
     */
    public $input;

    /**
     * @var string The output is the value which will actually rendered by the lexer. So lines which directly write to the output
     * buffer needs to fill in this variable.
     */
    public $output;

    /**
     * @var boolean Whether the current line is handled as "inline-line" or not. Inline lines have different effects when parsing the
     * end output. For example those can be skipped as they usual prepend the input value into the next line. 
     */
    protected $isInline = false;

    /**
     * @var boolean As certain elements has an end of newline but those are removed within the lexer opt to line methods we remember
     * this information here. If true this element has an \n element which has been original removed from input (as lines are spliited into
     * new lines).
     */
    protected $hadEndNewline = false;

    protected $hasNewline;
    /**
     * Constructor
     *
     * @param integer $index The numberic index of the row within all the lines.
     * @param string $input The input value from the line parser for the current line.
     * @param array $attributes
     * @param Lexer $lexer
     * @param boolean $hadNedNewline Whether this element orignali had an newline at the end.
     */
    public function __construct($index, $input, array $attributes, Lexer $lexer, $hadEndNewline, $hasNewline)
    {
        $this->index = $index;
        $this->input = $input;
        $this->attributes = $attributes;
        $this->lexer = $lexer;
        $this->hadEndNewline = $hadEndNewline;
        $this->hasNewline = $hasNewline;
    }

    public function hasNewline()
    {
        return $this->hasNewline;
    }

    public function hasEndNewline()
    {
        return $this->hadEndNewline;
    }

    public function isFirst()
    {
        return $this->previous() === false;
    }

    /**
     * Get the array with attributes, if any.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Whether the current line has attribute informations or not.
     *
     * @return boolean
     */
    public function hasAttributes()
    {
        return !empty($this->attributes);
    }

    /**
     * Get the value for a given attribute name, if not exists return false.
     *
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : false;
    }

    /**
     * Add a new value to the prepend array.
     * 
     * Certain elements needs to prepend values into the next element.
     *
     * @param string $value The value to prepend.
     * @return void
     */
    public function addPrepend($value)
    {
        $this->prepend[] = $value;
    }

    /**
     * Returns the string for the prepend values.
     *
     * @return string The prepend value for this line.
     */
    public function renderPrepend()
    {
        return implode("", array_unique($this->prepend));
    }

    public function while(callable $condition)
    {
        $iterate = true;
        $i = $this->getIndex();
        while ($iterate) {
            $line = $this->lexer->getLine($i);

            if (!$line) {
                $iterate = false;
                return;
            }
            $iterate = call_user_func_array($condition, [&$i, $line]);

            if ($iterate !== false) {
                $iterate = true;
            }
        }
    }

    /**
     * Iteration helper the go forward and backward in lines.
     *
     * @param Line $line
     * @param callable $condition The condition callable for the index
     * @param callable $fn The function which is returend to determine whether this line should be picked or not.
     * @return void
     */
    protected function iterate(Line $line, callable $condition, callable $fn)
    {
        $iterate = true;
        $i = $line->getIndex();
        
        while ($iterate) {
            $i = call_user_func($condition, $i);
            $elmn = $this->lexer->getLine($i);
            // no next element found
            if (!$elmn) {
                return false;
            }
            // fn match return current element.
            if (call_user_func($fn, $elmn)) {
                return $elmn;
            }
        }

        return false;
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
     * 
     * @param callable $fn A function in order to determined whether this is the next element or not, if not provided the first next element is returned.
     * @return Line
     */
    public function next($fn = null)
    {
        if ($fn === null) {
            return $this->lexer->getLine($this->index + 1);
        }

        return $this->iterate($this, function($i) {
            return ++$i;
        }, $fn);
    }

    /**
     * Get the previous line.
     * 
     * If no previous line exists, false is returned.
     *
     * ```php
     * $nextNotEmpty = $line->previous(function(Line $line) {
     *     return !$line->isEmpty();
     * });
     * ```
     * 
     * if true is returned this line will be assigned.
     * 
     * @param callable $fn A function in order to determined whether this is the previous element or not, if not provided the first previous element is returned.
     * @return Line
     */
    public function previous($fn = null)
    {
        if ($fn === null) {
            return $this->lexer->getLine($this->index - 1);
        }

        return $this->iterate($this, function($i) {
            return --$i;
        }, $fn);
    }

    /**
     * Setter method whether the current element is inline or not.
     */
    public function setAsInline()
    {
        $this->isInline = true;
    }

    /**
     * Whether current line is an inline line or not.
     *
     * @return boolean
     */
    public function getIsInline()
    {
        return $this->isInline;
    }

    /**
     * Getter method for the index of the line.
     *
     * @return integer
     */
    public function getIndex()
    {
        return $this->index;
    }
    
    /**
     * Set this line as picked.
     */
    public function setPicked()
    {
        $this->status = self::STATUS_PICKED;
    }

    /**
     * Whether current line is picked or not.
     *
     * @return boolean
     */
    public function isPicked()
    {
        return $this->status == self::STATUS_PICKED;
    }

    /**
     * Mark this line as done.
     */
    public function setDone()
    {
        $this->status = self::STATUS_DONE;
    }

    /**
     * Whether this line is done or not.
     *
     * @return boolean
     */
    public function isDone()
    {
        return $this->status == self::STATUS_DONE;
    }

    /**
     * Whether current line as empty content string or not.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->input == '' && empty($this->prepend);
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

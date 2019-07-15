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
     * @var array An array with values which can be prependend to the actuall input string. This is mainly used if inline
     * elements are passed to the next "not" inline element.
     */
    public $prepend = [];

    /**
     * @var string The input string which is assigned from the line parser. This is the actual content of the line itself!
     * @deprecated Deprecated since 1.2.0 will be removed in 2.0 use getInput() instead.
     */
    public $input;

    /**
     * @var string The output is the value which will actually rendered by the lexer. So lines which directly write to the output
     * buffer needs to fill in this variable.
     */
    public $output;

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
     * @var boolean Whether the current line is handled as "inline-line" or not. Inline lines have different effects when parsing the
     * end output. For example those can be skipped as they usual prepend the input value into the next line.
     */
    protected $isInline = false;

    /**
     * @var boolean Whether the current line is already escaped by a listener. If this is false, the next listener should preferable do so.
     * If this is true, it should not be done again by a next listener.
     * @since 1.2.0
     */
    protected $isEscaped = false;

    /**
     * @var boolean As certain elements has an end of newline but those are removed within the lexer opt to line methods we remember
     * this information here. If true this element has an \n element which has been original removed from input (as lines are spliited into
     * new lines).
     */
    protected $hadEndNewline = false;

    /**
     * @var boolean Whether this line has a newline or not, this information is already provided by the lines to ops method.
     */
    protected $hasNewline;

    /** @var boolean Whether this line used to have attributes but due to filtering now only consists of text */
    protected $textOnly;
    
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

    public function isTextOnly()
    {
        return $this->textOnly;
    }

    public function setAsTextOnly()
    {
        $this->textOnly = true;
    }



    /**
     * Whether the current line had a new line char or not, this is very important in terms of finding out wether its a block
     * element or inline element.
     *
     * This informations is assigned in the opsToLine() method in the lexer object.
     *
     * @return boolean
     */
    public function hasNewline()
    {
        return $this->hasNewline;
    }

    /**
     * Whether this line as an end newline char or not.
     *
     * @return boolean
     */
    public function hasEndNewline()
    {
        return $this->hadEndNewline;
    }

    /**
     * Whether this line is the first line or not.
     *
     * @return boolean
     */
    public function isFirst()
    {
        return $this->previous() === false;
    }

    /**
     * Get the Lexer
     *
     * @since 1.2.0
     * @return Lexer
     */
    public function getLexer()
    {
        return $this->lexer;
    }

    /**
     * Get the line's input in a safe way.
     *
     * Escaping for html is done if this wasn't done by a previous listener already.
     *
     * @since 1.2.0
     * @return string
     */
    public function getInput()
    {
        if ($this->isEscaped()) {
            return $this->input;
        }
        
        return $this->lexer->escape($this->getUnsafeInput());
    }

    /**
     * Get the raw line's input, this might not be escaped for html context.
     *
     * > Note it could be escaped if a previous inline listener updated the input value
     *
     * @since 1.2.0
     * @return string
     */
    public function getUnsafeInput()
    {
        return $this->input;
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
     * Certain elements needs to prepend values into the next element. The Line argument is required in order to 
     * ensure the correct index for the prepend element.
     * 
     * @param string $value The value to prepend.
     * @param Line $line The line which does the prepend, this is used to ensure the correctly order index of the elements. {@since 1.3.3}
     * @return void
     */
    public function addPrepend($value, Line $line)
    {
        $this->prepend[$line->getIndex()] = $value;
    }

    /**
     * Returns the string for the prepend values.
     *
     * @return string The prepend value for this line.
     */
    public function renderPrepend()
    {
        ksort($this->prepend);
        return implode("", array_unique($this->prepend));
    }

    /**
     * While trough lines forward or backwards define trough index until false is returned.
     *
     * An example how to while trough lines, increasing (down) the index until a certain condition
     * ($line->isFirst) happens writing lint input into a buffer variable.
     *
     * > Keep in mind that while() will contain the line where the function applys, so the first line will always be
     * > the line you apply the while() function.
     *
     * ```php
     * $buffer = null;
     *
     * $line->while(function (&$index, Line $line) use (&$buffer) {
     *     $index++;
     *     $buffer.= $line->input;
     *
     *     if ($line->isFirst()) {
     *         return false;
     *     }
     * });
     *
     * echo $buffer;
     * ```
     *
     * > Keep in mind that `false` must be returned to stop the while process.
     *
     * @param callable $condition A callable which requires 2 params, the first is the index which is passed as reference,
     * second is the current line.
     * @return void
     */
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
     * While loop down (to the next elements) until false is returend.
     *
     * > This metod wont return the line.
     *
     * @param callable $condition The while condition until false is returned.
     * @since 1.3.0
     */
    public function whileNext(callable $condition)
    {
        $next = $this->next();
        if ($next) {
            return $next->while(function (&$index, Line $line) use ($condition) {
                $index++;
                return call_user_func($condition, $line);
            });
        }
    }
    
    /**
     * While loop up (to the previous elements) until false is returend.
     *
     * > This metod wont return the line.
     *
     * @param callable $condition The while condition until false is returned.
     * @since 1.3.0
     */
    public function whilePrevious(callable $condition)
    {
        $previous = $this->previous();
        if ($previous) {
            return $previous->while(function (&$index, Line $line) use ($condition) {
                $index--;

                return call_user_func($condition, $line);
            });
        }
    }

    /**
     * Iteration helper the go forward and backward in lines.
     *
     * The condition contains whether index should go up or down.
     *
     * ```php
     * return $this->iterate($line, function ($i) {
     *    return $i+1;
     * }, function(Line $line) {
     *      // will stop the process and return this current line
     *      return true;
     * });
     * ```
     *
     * @param Line $line
     * @param callable $condition The condition callable for the index
     * @param callable $fn The function which is returend to determine whether this line should be picked or not.
     * @return boolean|Line
     */
    protected function iterate(Line $line, callable $condition, callable $fn)
    {
        $i = $line->getIndex();
        $iterate = true;
        $response = false;
        while ($iterate) {
            $i = call_user_func($condition, $i);
            $elmn = $this->lexer->getLine($i);
            // no next element found
            if (!$elmn) {
                $iterate = false;
            } elseif (call_user_func($fn, $elmn)) {
                // fn match (return true) return current element.
                $response = $elmn;
                $iterate = false;
            }
        }

        return $response;
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

        return $this->iterate($this, function ($i) {
            return $i+1;
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

        return $this->iterate($this, function ($i) {
            return $i-1;
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
    public function isInline()
    {
        return $this->isInline;
    }

    /**
     * Setter method whether the current line is escaped or not.
     *
     * @since 1.2.0
     */
    public function setAsEscaped()
    {
        $this->isEscaped = true;
    }

    /**
     * Whether the current line is escaped or not.
     *
     * @since 1.2.0
     * @return boolean
     */
    public function isEscaped()
    {
        return $this->isEscaped;
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
     * Whether current line is picked or not. If the line has been picked an is marked as
     * done, is picked will return false.
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

    /**
     * Check whether insert is json/array input. if yes return the requres key.
     *
     * @param string $key The key from the json array
     * @return mixed
     */
    public function insertJsonKey($key)
    {
        if (!$this->isJsonInsert()) {
            return false;
        }

        $insert = $this->getArrayInsert();

        return array_key_exists($key, $insert) ? $insert[$key] : false;
    }

    private $_debug = [];

    /**
     * Add debug message for this line if {{Lexer::$debug}} is enabled.
     *
     * @param string $message The message which should be logged.
     * @since 1.3.0
     */
    public function debugInfo($message)
    {
        if ($this->lexer->debug) {
            $this->_debug[] = $message;
        }
    }

    /**
     * Return an array with all debug informations
     *
     * @return array
     * @since 1.3.0
     */
    public function getDebugInfo() : array
    {
        return $this->_debug;
    }
}

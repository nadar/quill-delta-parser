<?php

namespace nadar\quill;

use nadar\quill\listener\Heading;
use nadar\quill\listener\Text;
use nadar\quill\listener\Lists;
use nadar\quill\listener\Bold;
use nadar\quill\listener\Blockquote;
use nadar\quill\listener\Link;
use nadar\quill\listener\Italic;
use nadar\quill\listener\Strike;
use nadar\quill\listener\Underline;
use nadar\quill\listener\Video;
use nadar\quill\listener\Image;
use nadar\quill\listener\Color;

/**
 * Lexer Delta Parser.
 *
 * The lexer class represents the main thread in how delta input is processed and rendered.
 *
 * Basically Listeneres can watch every line of delta and interact with the line, which means
 * reading input and writting to output and mark this line as done, so other listeneres won't
 * take care of this line as well.
 *
 * ## Basic concept
 *
 * Listeneres are grouped in 2 types:
 *
 * + inline: For elements which are only inline applied, like writing bold or italic
 * + block: Used when the line represents a full html element like heading or lists
 *
 * Inside this group types there are 2 prioirities:
 *
 * + early bird: This is default value, the early bird catches the worm...
 * + garbage collector: This is mainly used for the text listenere which generates the paragraphs and can only be done at the very end of the process.
 *
 * Every listenere has two methods a process() and render():
 *
 * + process: is triggered by every line, so the listenere can choose whether he wants to pick this line, interact or not.
 * + render: after all lines are processed, every listenered triggers the render() method once, so the picked line from process can be "further" processed and rendered.
 *
 * ## Lifecycle
 *
 * 1. lines will be generated
 * 2. lines foreached and inline early bird listeners run process() method.
 * 3. lines foreached and inline garbage collector listeneres run process() method.
 * 4. lines foreached and block early bird listeneres run process() method.
 * 5. lines foreached and block garbage collector listenere run process() method.
 * 6. inline listeneres foreach and run render() method.
 * 7. block listeneres foreach and run render() method.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Lexer
{
    /**
     * @var string An internal string for newlines, this makes it more easy to debug instead of using \n (newlines).
     */
    const NEWLINE_EXPRESSION = '<!-- <![CDATA[NEWLINE]]> -->';

    /**
     * @var boolean Whether input should be escaped by listeners when mixed with html elements.
     * Note that a specific listener can decide to not escape if their output should be raw html.
     * Defaults to false, will default to true in the next major version.
     * @since 1.2.0
     */
    public $escapeInput = false;

    /**
     * @var boolean These flags are used for escaping values for mixing with a html context.
     * @since 1.2.0
     */
    public $escapeFlags = ENT_QUOTES|ENT_HTML5;

    /**
     * @var boolean The encoding is used for escaping values for mixing with a html context.
     * @since 1.2.0
     */
    public $escapeEncoding = 'UTF-8';

    /**
     * @var boolean Whether debbuging is enabled or not. If enabled some html comments will be added to certain elements.
     */
    public $debug = false;

    /**
     * @var array|string The delta ops json as string or as already parsed array
     */
    protected $json;

    /**
     * @var array The listeneres grouped by type and priority.
     */
    protected $listeners = [
        Listener::TYPE_INLINE => [
            Listener::PRIORITY_EARLY_BIRD => [],
            Listener::PRIORITY_GARBAGE_COLLECTOR => [],
        ],
        Listener::TYPE_BLOCK => [
            Listener::PRIORITY_EARLY_BIRD => [],
            Listener::PRIORITY_GARBAGE_COLLECTOR => [],
        ],
    ];

    /**
     * Initializer
     *
     * @param string $json The delta ops json as string or as already parsed array.
     * @param boolean $loadBuiltinListeneres Whether the built in listeneres should be loaded or not.
     */
    public function __construct($json, $loadBuiltinListeneres = true)
    {
        $this->json = $json;

        if ($loadBuiltinListeneres) {
            $this->loadBuiltinListeneres();
        }
    }

    /**
     * Loads the library built in listeneres.
     */
    public function loadBuiltinListeneres()
    {
        $this->registerListener(new Bold);
        $this->registerListener(new Italic);
        $this->registerListener(new Color);
        $this->registerListener(new Link);
        $this->registerListener(new Video);
        $this->registerListener(new Image);
        $this->registerListener(new Strike);
        $this->registerListener(new Underline);
        $this->registerListener(new Heading);
        $this->registerListener(new Text);
        $this->registerListener(new Lists);
        $this->registerListener(new Blockquote);
    }

    /**
     * Register a new listenere.
     *
     * @param Listener $listener
     */
    public function registerListener(Listener $listener)
    {
        $this->listeners[$listener->type()][$listener->priority()][get_class($listener)] = $listener;
    }

    /**
     * Get the input json as array.
     *
     * @return array The json as array formated.
     */
    public function getJsonArray() : array
    {
        return is_array($this->json) ? $this->json : self::decodeJson($this->json);
    }

    /**
     * Get the ops section from the json otherwise json array.
     *
     * @return array
     */
    public function getOps() : array
    {
        return isset($this->getJsonArray()['ops']) ? $this->getJsonArray()['ops'] : $this->getJsonArray();
    }

    /**
     * Get the line object for a given id/row/index.
     *
     * @param integer $index The index of the line.
     * @return Line
     */
    public function getLine($index)
    {
        return isset($this->_lines[$index]) ? $this->_lines[$index] : false;
    }

    /**
     * @return array Returns an array with all line objects.
     */
    public function getLines() : array
    {
        return $this->_lines;
    }

    private $_lines = [];

    /**
     * Convert the arrray operations array into lines
     *
     * @param array $ops An array from json with ops data in delta format.
     * @return array An array with Line objects
     */
    protected function opsToLines(array $ops)
    {
        $lines = [];
        $i = 0;
        foreach ($ops as $delta) {
            // replace newline chars with internal expression
            $insert = $this->replaceNewlineWithExpression($delta['insert']);
            // if its an empty "newline-line"
            if ($insert == self::NEWLINE_EXPRESSION) {
                $lines[$i] = new Line($i, '', isset($delta['attributes']) ? $delta['attributes'] : [], $this, true, true);
                $i++;
            } else {
                // remove the last newline from the line, as it will be splited into lines anyhow.
                $line = $this->removeLastNewline($insert);
                // check if the original input was changed, in order to determine whether current line had new line char.
                $hasEndNewline = ($line !== $insert);
                // check if current input string has a new line char in the string
                $hasNewline = $this->lineHasNewline($line);
                // split the input string into parts / lines.
                $parts = explode(self::NEWLINE_EXPRESSION, $line);
                foreach ($parts as $index => $value) {
                    // check if this line had the end newline
                    $hadEndNewline = ($hasEndNewline && ($index + 1) == count($parts)) ? true : false;
                    $lines[$i] = new Line($i, $value, isset($delta['attributes']) ? $delta['attributes'] : [], $this, $hadEndNewline, $hasNewline);
                    $i++;
                }
            }
        }

        return $lines;
    }

    /**
     * Whether the current line as a newline char.
     *
     * @param string $input
     * @return string
     */
    protected function lineHasNewline($input)
    {
        return (strpos($input, self::NEWLINE_EXPRESSION) !== false) ? true : false;
    }

    /**
     * Undocumented function
     *
     * @param [type] $string
     * @return void
     */
    protected function replaceNewlineWithExpression($string)
    {
        return str_replace(PHP_EOL, self::NEWLINE_EXPRESSION, $string);
    }

    /**
     * Undocumented function
     *
     * @param [type] $insert
     * @return void
     */
    protected function removeLastNewline($insert)
    {
        // plugsin can have array values as text.
        if (is_array($insert)) {
            // convert the array into a json string
            return json_encode($insert);
        }

        $expLength = strlen(self::NEWLINE_EXPRESSION);
        // remove new line from the end of the string
        // as this explode split well be done anyhow or its already part of a new line
        if (substr($insert, -$expLength) == self::NEWLINE_EXPRESSION) {
            return substr($insert, 0, -$expLength);
        }

        return $insert;
    }

    /**
     * Undocumented function
     *
     * @param Line $line
     * @param [type] $type
     * @return void
     */
    protected function processListeners(Line $line, $type)
    {
        foreach ($this->listeners[$type] as $prios) {
            foreach ($prios as $listener) {
                $listener->process($line);
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param [type] $type
     * @return void
     */
    protected function renderListeneres($type)
    {
        foreach ($this->listeners[$type] as $prios) {
            foreach ($prios as $listener) {
                $listener->render($this);
            }
        }
    }

    /**
     * Renders the current delta into a html string.
     *
     * @return string The html code for the given delta input.
     */
    public function render()
    {
        $this->_lines = $this->opsToLines($this->getOps());

        foreach ($this->_lines as $line) {
            $this->processListeners($line, Listener::TYPE_INLINE);
            $this->processListeners($line, Listener::TYPE_BLOCK);
        }

        $this->renderListeneres(Listener::TYPE_INLINE);
        $this->renderListeneres(Listener::TYPE_BLOCK);

        $buff = null;
        foreach ($this->_lines as $line) {
            $buff.= $line->output;
        }

        return $buff;
    }

    /**
     * Checks if a string is a json or not.
     *
     * Example values which return true:
     *
     * ```php
     * Json::isJson('{"123":"456"}');
     * Json::isJson('{"123":456}');
     * Json::isJson('[{"123":"456"}]');
     * Json::isJson('[{"123":"456"}]');
     * ```
     *
     * @param mixed $value The value to test if its a json or not.
     * @return boolean Whether the string is a json or not.
     */
    public static function isJson($value)
    {
        if (!is_scalar($value)) {
            return false;
        }
        
        $firstChar = substr($value, 0, 1);
        
        if ($firstChar !== '{' && $firstChar !== '[') {
            return false;
        }
        
        $json_check = json_decode($value);
        
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Decode a given json string into a php array.
     *
     * @param string $json Input json
     * @return array
     */
    public static function decodeJson($json)
    {
        return json_decode($json, true);
    }

    /**
     * Escape plain text output before mixing in a html context.
     * 
     * This should be used on any input or attributes in a delta operation.
     * Double encoding is prevented on already encoded characters.
     * For escaping input, use Line->getInput() instead. Otherwise an inline listener would encode the tags from another nested inline listener.
     * 
     * @since 1.2.0
     * @param string $value The value to escape.
     * @return string The escaped value, safe for usage in html, as long as $escapeInput is set to true.
     */
    public function escape($value)
    {
        if (!$this->escapeInput) {
            return $value;
        }
        
        return htmlspecialchars($value, $this->escapeFlags, $this->escapeEncoding, $double = false);
    }
}

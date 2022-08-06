<?php

namespace nadar\quill;

use nadar\quill\listener\Align;
use nadar\quill\listener\Blockquote;
use nadar\quill\listener\Bold;
use nadar\quill\listener\CodeBlock;
use nadar\quill\listener\Color;
use nadar\quill\listener\Font;
use nadar\quill\listener\Heading;
use nadar\quill\listener\Image;
use nadar\quill\listener\Italic;
use nadar\quill\listener\Link;
use nadar\quill\listener\Lists;
use nadar\quill\listener\Script;
use nadar\quill\listener\Strike;
use nadar\quill\listener\Text;
use nadar\quill\listener\Underline;
use nadar\quill\listener\Video;

/**
 * Lexer Delta Parser.
 *
 * The lexer class represents the main thread in how delta input is processed and rendered.
 *
 * Basically listeners can watch every line of delta and interact with the line, which means
 * reading input and writting to output and mark this line as done, so other listeners won't
 * take care of this line as well.
 *
 * ## Basic concept
 *
 * Listeners are grouped in 2 types:
 *
 * + inline: For elements which are only inline applied, like writing bold or italic
 * + block: Used when the line represents a full html element like heading or lists
 *
 * Inside this group types there are 2 prioirities:
 *
 * + early bird: This is default value, the early bird catches the worm...
 * + garbage collector: This is mainly used for the text listener which generates the paragraphs and can only be done at the very end of the process.
 *
 * Every listener has two methods a process() and render():
 *
 * + process: is triggered by every line, so the listener can choose whether he wants to pick this line, interact or not.
 * + render: after all lines are processed, every listener triggers the render() method once, so the picked line from process can be "further" processed and rendered.
 *
 * ## Lifecycle
 *
 * 1. lines will be generated
 * 2. lines foreached and inline early bird listeners run process() method.
 * 3. lines foreached and inline garbage collector listeners run process() method.
 * 4. lines foreached and block early bird listeners run process() method.
 * 5. lines foreached and block garbage collector listener run process() method.
 * 6. inline listeners foreach and run render() method.
 * 7. block listeners foreach and run render() method.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Lexer
{
    /**
     * @var string The quill deltas newline is always \n, thefore do not use PHP_EOL as its different in win platforms.
     * @see https://quilljs.com/guides/designing-the-delta-format/
     * @see https://github.com/nadar/quill-delta-parser/issues/12
     * @since 1.3.1
     */
    public const DELTA_EOL = "\n";

    /**
     * @var string An internal string for newlines, this makes it more easy to debug instead of using \n (newlines).
     */
    public const NEWLINE_EXPRESSION = '<!-- <![CDATA[NEWLINE]]> -->';

    /**
     * @var boolean Whether input should be escaped by listeners when mixed with html elements.
     * Note that a specific listener can decide to not escape if their output should be raw html.
     * If script and other insecure input values should be allowed this can be turned off but its not recommend!
     * @since 1.2.0
     */
    public $escapeInput = true;

    /**
     * @var int These flags are used for escaping values for mixing with a html context.
     * @since 1.2.0
     */
    public $escapeFlags = ENT_QUOTES|ENT_HTML5;

    /**
     * @var string The encoding is used for escaping values for mixing with a html context.
     * @since 1.2.0
     */
    public $escapeEncoding = 'UTF-8';

    /**
     * @var boolean Whether debbuging is enabled or not. If enabled lines can contain debugInfo messages which can be retrieved with {{Debug}} class.
     */
    public $debug = false;

    /**
     * @var array<mixed>|string The delta ops json as string or as already parsed array
     */
    protected $json;

    /**
     * @var array<Line>
     */
    private $_lines = [];

    /**
     * @var array<mixed> The listeners grouped by type and priority.
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
     * @param string|array<mixed> $json The delta ops json as string or as already parsed array.
     * @param boolean $loadBuiltinListeners Whether the built in listeners should be loaded or not.
     */
    public function __construct($json, $loadBuiltinListeners = true)
    {
        $this->json = $json;

        if ($loadBuiltinListeners) {
            $this->loadBuiltinListeners();
        }
    }

    /**
     * Loads the library built in listeners.
     * 
     * @return void
     */
    public function loadBuiltinListeners()
    {
        $this->registerListener(new Image());
        $this->registerListener(new Bold());
        $this->registerListener(new Italic());
        $this->registerListener(new Color());
        $this->registerListener(new Link());
        $this->registerListener(new Video());
        $this->registerListener(new Strike());
        $this->registerListener(new Underline());
        $this->registerListener(new Heading());
        $this->registerListener(new CodeBlock());
        $this->registerListener(new Text());
        $this->registerListener(new Lists());
        $this->registerListener(new Blockquote());
        $this->registerListener(new Font());
        $this->registerListener(new Script());
        $this->registerListener(new Align());
    }

    /**
     * Register a new listener.
     *
     * @param Listener $listener
     * @return void
     */
    public function registerListener(Listener $listener)
    {
        $this->listeners[$listener->type()][$listener->priority()][get_class($listener)] = $listener;
    }

    /**
     * Overrite an existing listener with a new object
     *
     * An example could when you like to provide more options or access other elements which are not covered by the base class
     * so you can extend from the built in listeners and overrite them. This keeps also the hyrarchical level of the elements.
     *
     * ```php
     * $lexer->overwriteListener(new Image, new MyOwnImage);
     * ```
     *
     * As the `new Image` listener is already registered, it will just replace the object with `new MyOwnImage`.
     *
     * @param Listener $listener The already registered listener object
     * @param Listener $new The new listener object
     * @see https://github.com/nadar/quill-delta-parser/issues/55
     * @since 2.8.0
     * @return void
     */
    public function overwriteListener(Listener $listener, Listener $new)
    {
        $this->listeners[$listener->type()][$listener->priority()][get_class($listener)] = $new;
    }

    /**
     * Get the input json as array.
     *
     * @return array<mixed> The json as array formated.
     */
    public function getJsonArray(): array
    {
        return is_array($this->json) ? $this->json : self::decodeJson($this->json);
    }

    /**
     * Get the ops section from the json otherwise json array.
     *
     * @return array<mixed>
     */
    public function getOps(): array
    {
        return isset($this->getJsonArray()['ops']) ? $this->getJsonArray()['ops'] : $this->getJsonArray();
    }

    /**
     * Get the line object for a given id/row/index.
     *
     * @param integer $index The index of the line.
     * @return Line|boolean
     */
    public function getLine($index)
    {
        return isset($this->_lines[$index]) ? $this->_lines[$index] : false;
    }

    /**
     * @return array<Line> Returns an array with all line objects.
     */
    public function getLines(): array
    {
        return $this->_lines;
    }

    /**
     * Convert the arrray operations array into lines
     *
     * @param array<mixed> $ops An array from json with ops data in delta format.
     * @return array<Line> An array with Line objects
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
                $insert = $this->normalizeInsert($insert);
                // remove the last newline from the line, as it will be splited into lines anyhow.
                $line = $this->removeLastNewline($insert);
                // check if the original input was changed, in order to determine whether current line had new line char.
                $hasEndNewline = ($line !== $insert);

                // check if original insert string has a new line char in the string
                $hasNewline = $this->lineHasNewline($insert);
                // split the input string into parts / lines.
                $parts = explode(self::NEWLINE_EXPRESSION, $line);
                $count = count($parts);
                foreach ($parts as $index => $value) {
                    // check if this line had the end newline
                    $isLast = ($index + 1) == $count;
                    $hadEndNewline = $isLast && $hasEndNewline ? true : false;
                    if ($hasNewline && $isLast && !$hasEndNewline) {
                        $hasNewline = false;
                    }
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
     * @return boolean
     */
    protected function lineHasNewline($input)
    {
        return (strpos($input, self::NEWLINE_EXPRESSION) !== false) ? true : false;
    }

    /**
     * Replace new lines with an internal representation that aids debugging
     *
     * @param array<mixed>|string $input
     * @return array<mixed>|string
     */
    protected function replaceNewlineWithExpression($input)
    {
        return is_array($input) ? $input : str_replace(self::DELTA_EOL, self::NEWLINE_EXPRESSION, $input);
    }

    /**
     * Normamlize the insert values into json
     *
     * @param array<mixed>|string $insert
     * @return string
     * @since 1.2.2
     */
    protected function normalizeInsert($insert)
    {
        // plugins can have array values as text.
        if (is_array($insert)) {
            // convert the array into a json string
            return json_encode($insert);
        }

        return $insert;
    }

    /**
     * Remove the last newline expression from a string
     *
     * @param string $insert
     * @return string
     */
    protected function removeLastNewline($insert)
    {
        $expLength = strlen(self::NEWLINE_EXPRESSION);
        // remove new line from the end of the string
        // as this explode split well be done anyhow or its already part of a new line
        if (substr($insert, -$expLength) == self::NEWLINE_EXPRESSION) {
            return substr($insert, 0, -$expLength);
        }

        return $insert;
    }

    /**
     * Process all listeneres for a given type
     *
     * @param Line $line
     * @param int $type
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
     * Render all listeners for a given type
     *
     * @param int $type
     * @return void
     */
    protected function renderListeners($type)
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

        $this->renderListeners(Listener::TYPE_INLINE);
        $this->renderListeners(Listener::TYPE_BLOCK);

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
     * @return array<mixed>
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

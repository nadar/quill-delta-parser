<?php

namespace nadar\quill;

use nadar\quill\listener\Heading;
use nadar\quill\listener\Text;
use nadar\quill\listener\Lists;
use nadar\quill\listener\Bold;
use nadar\quill\listener\Blockquote;
use nadar\quill\listener\Link;
use nadar\quill\listener\Italic;

class Lexer
{
    const NEWLINE_EXPRESSION = '<!--CDATA:NEWLINE-->';

    protected $json;

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

    public $debug = false;

    public function __construct(string $json)
    {
        $this->json = $json;
    }

    public function initBuiltInListeners()
    {
        $this->registerListener(new Bold);
        $this->registerListener(new Italic);
        $this->registerListener(new Link);
        $this->registerListener(new Heading);
        $this->registerListener(new Text);
        $this->registerListener(new Lists);
        $this->registerListener(new Blockquote);
    }

    public function registerListener(Listener $listener)
    {
        $this->listeners[$listener->type()][$listener->priority()][] = $listener;
    }

    public function getJsonArray() : array
    {
        return json_decode($this->json, true);
    }

    public function getOps() : array
    {
        return isset($this->getJsonArray()['ops']) ? $this->getJsonArray()['ops'] : $this->getJsonArray();
    }

    public function getLine($id)
    {
        return isset($this->_lines[$id]) ? $this->_lines[$id] : false;
    }

    public function getLines() : array
    {
        return $this->_lines;
    }

    private $_lines = [];

    protected function opsToLines(array $ops)
    {
        $lines = [];
        $i = 0;
        foreach ($ops as $key => $delta) {
            $insert = $this->replaceNewlineWithExpression($delta['insert']);
            if ($insert == self::NEWLINE_EXPRESSION) {
                $lines[$i] = new Line($i, '', isset($delta['attributes']) ? $delta['attributes'] : [], $this);
                $i++;
            } else {
                foreach (explode(self::NEWLINE_EXPRESSION, $this->removeLastNewline($insert)) as $value) {
                    $lines[$i] = new Line($i, $value, isset($delta['attributes']) ? $delta['attributes'] : [], $this);
                    $i++;
                }
            }
        }

        return $lines;
    }

    protected function replaceNewlineWithExpression($string)
    {
        return str_replace(PHP_EOL, self::NEWLINE_EXPRESSION, $string);
    }

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

    protected function processListeners(Line $line, $type)
    {
        foreach ($this->listeners[$type] as $prios) {
            foreach ($prios as $listener) {
                $listener->process($line);
            }
        }
    }

    protected function renderListeneres($type)
    {
        foreach ($this->listeners[$type] as $prios) {
            foreach ($prios as $listener) {
                $listener->render($this);
            }
        }
    }

    public function render()
    {
        $this->_lines = $this->opsToLines($this->getOps());

        foreach ($this->_lines as $row => $line) {
            $this->processListeners($line, Listener::TYPE_INLINE);
            $this->processListeners($line, Listener::TYPE_BLOCK);
        }

        $this->renderListeneres(Listener::TYPE_INLINE);
        $this->renderListeneres(Listener::TYPE_BLOCK);

        $buff = null;
        foreach ($this->_lines as $row => $line) {
            $buff.= $line->output;
        }

        return $buff;
    }
}

<?php

namespace nadar\quill;

use nadar\quill\listener\Heading;
use nadar\quill\listener\Text;
use nadar\quill\listener\Lists;
use nadar\quill\listener\Bold;
use nadar\quill\listener\Blockquote;


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

    public function __construct(string $json)
    {
        $this->json = $json;
    }

    public function initBuiltInListeners()
    {
        $this->registerListener(New Bold);
        //&$this->registerListener(new Italic);
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

    public function getLines()
    {
        return $this->_lines;
    }

    private $_lines = [];

    public function render()
    {
        $this->opsToLines($this->getOps());

        foreach ($this->_lines as $row => $line) {
            foreach ($this->listeners[Listener::TYPE_INLINE] as $prios) {
                foreach ($prios as $listener) {
                    $listener->process($line);
                }
            }
            foreach ($this->listeners[Listener::TYPE_BLOCK] as $prios) {
                foreach ($prios as $listener) {
                    $listener->process($line);
                }
            }
        }

        foreach ($this->listeners[Listener::TYPE_INLINE] as $prios) {
            foreach ($prios as $listener) {
                $listener->render($this);
            }
        }
        foreach ($this->listeners[Listener::TYPE_BLOCK] as $prios) {
            foreach ($prios as $listener) {
                $listener->render($this);
            }
        }

        $buff = null;
        foreach ($this->_lines as $row => $line) {
            $buff.= $line->output;
        }

        return $buff;
    }

    protected function opsToLines(array $ops)
    {
        $i = 0;
        foreach ($ops as $key => $delta) {
            $insert = str_replace(PHP_EOL, self::NEWLINE_EXPRESSION, $delta['insert']);

            //if ($insert == self::NEWLINE_EXPRESSION) {
            //    $this->_lines[$i] = new Line($i, '', isset($delta['attributes']) ? $delta['attributes'] : [], $this);
            //    $i++;
            //} else {
                // remove new line from the end of the string
                // as this explode split well be done anyhow or its already part of a new line
                if (substr($insert, -strlen(self::NEWLINE_EXPRESSION)) == self::NEWLINE_EXPRESSION) {
                    //$insert = substr($insert, 0, -strlen(self::NEWLINE_EXPRESSION));
                }
                
                foreach (explode(self::NEWLINE_EXPRESSION, $insert) as $value) {
                    $this->_lines[$i] = new Line($i, $value, isset($delta['attributes']) ? $delta['attributes'] : [], $this);
                    $i++;
                }
            //}
        }
    }
}
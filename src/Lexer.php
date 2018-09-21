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
        $expLength = strlen(self::NEWLINE_EXPRESSION);
        $lines = [];
        $i = 0;
        foreach ($ops as $key => $delta) {
            $insert = str_replace(PHP_EOL, self::NEWLINE_EXPRESSION, $delta['insert']);

            if ($insert == self::NEWLINE_EXPRESSION) {
                $lines[$i] = new Line($i, '', isset($delta['attributes']) ? $delta['attributes'] : [], $this);
                $i++;
            } else {
                // remove new line from the end of the string
                // as this explode split well be done anyhow or its already part of a new line
                if (substr($insert, -$expLength) == self::NEWLINE_EXPRESSION) {
                    $insert = substr($insert, 0, -$expLength);
                }

                // remove the first line from the start from the string as this is already a line by the previous element
                // only if two \n\n are available after each other
                //if (substr($insert, 0, ($expLength*2)) == self::NEWLINE_EXPRESSION . self::NEWLINE_EXPRESSION) {
                //    $insert = substr($insert, $expLength);
                //}
                
                foreach (explode(self::NEWLINE_EXPRESSION, $insert) as $value) {
                    $lines[$i] = new Line($i, $value, isset($delta['attributes']) ? $delta['attributes'] : [], $this);
                    $i++;
                }
            }
        }

        return $lines;
    }

    public function render()
    {
        $this->_lines = $this->opsToLines($this->getOps());

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

    protected function getNotDoneLines()
    {
        $lines = [];
        foreach ($this->getLines() as $line) {
            if (!$line->isDone()) {
                $lines[] = $line;
            }
        }
        return $lines;
    }

    protected function getNotPickedLines()
    {
        $lines = [];
        foreach ($this->getLines() as $line) {
            if (!$line->isPicked()) {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    public function debugPrint()
    {
        $d = "QUILL DELTA LEXER DEBUG".PHP_EOL;
        $d.= "============= SUMMARY ===================" . PHP_EOL;
        $d.= "Lines:" . count($this->getLines()) . PHP_EOL;
        $d.= "Lines not done: " . count($this->getNotDoneLines()) . PHP_EOL;
        $d.= "Lines not picked: " . count($this->getNotPickedLines()) . PHP_EOL;
        $d.= "============= NOT PICKED LINES ==================<table border=1>";
        foreach ($this->getNotPickedLines() as $line) {
            $d.= '<tr><td>#' . $line->row . '</td><td>' . htmlentities($line->input, ENT_QUOTES) . '</td></tr>';
        }
        $d.= "</table><p>============= NOT DONE LINES ==================</p><table border=1>";
        foreach ($this->getNotDoneLines() as $line) {
            $d.= '<tr><td>#' . $line->row . '</td><td>' . htmlentities($line->input, ENT_QUOTES) . '</td></tr>';
        }
        $d.= "</table><p>============= LINE BY LINE ==================</p><table border=1>";
        foreach ($this->getLines() as $line) {
            $d.= '<tr>';
            $d.= '<td>#' . $line->row . '</td>';
            $d.= '<td>'.htmlentities($line->input, ENT_QUOTES) . '</td>';
            $d.= '<td>'.var_export($line->isInline, true).'</td>';
            $d.= '<td>'.var_export($line->isPicked(), true).'</td>';
            $d.= '<td>'.var_export($line->attributes, true).'</td>';
            $d.= '</tr>';
        }
        $d.= '</table>';
        echo nl2br($d);
    }
}

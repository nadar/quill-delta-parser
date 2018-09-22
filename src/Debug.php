<?php

namespace nadar\quill;

class Debug
{
    public $lexer;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;   
    }

    protected function getNotDoneLines()
    {
        $lines = [];
        foreach ($this->lexer->getLines() as $line) {
            if (!$line->isDone()) {
                $lines[] = $line;
            }
        }
        return $lines;
    }

    protected function getNotPickedLines()
    {
        $lines = [];
        foreach ($this->lexer->getLines() as $line) {
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
        $d.= "Lines:" . count($this->lexer->getLines()) . PHP_EOL;
        $d.= "Lines not done: " . count($this->lexer->getNotDoneLines()) . PHP_EOL;
        $d.= "Lines not picked: " . count($this->lexer->getNotPickedLines()) . PHP_EOL;
        $d.= "============= NOT PICKED LINES ==================<table border=1>";
        foreach ($this->lexer->getNotPickedLines() as $line) {
            $d.= '<tr><td>#' . $line->row . '</td><td>' . htmlentities($line->input, ENT_QUOTES) . '</td></tr>';
        }
        $d.= "</table><p>============= NOT DONE LINES ==================</p><table border=1>";
        foreach ($this->lexer->getNotDoneLines() as $line) {
            $d.= '<tr><td>#' . $line->row . '</td><td>' . htmlentities($line->input, ENT_QUOTES) . '</td></tr>';
        }
        $d.= "</table><p>============= LINE BY LINE ==================</p><table border=1>";
        foreach ($this->lexer->getLines() as $line) {
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
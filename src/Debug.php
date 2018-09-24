<?php

namespace nadar\quill;

/**
 * Debug Object.
 *
 * The Debug class can return informations in a readable way from a lexer object.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
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
        $d.= "Lines not done: " . count($this->getNotDoneLines()) . PHP_EOL;
        $d.= "Lines not picked: " . count($this->getNotPickedLines()) . PHP_EOL;
        $d.= "============= NOT PICKED LINES ==================<table border=1>";
        foreach ($this->getNotPickedLines() as $line) {
            $d.= '<tr><td>#' . $line->getIndex() . '</td><td>' . htmlentities($line->input, ENT_QUOTES) . '</td></tr>';
        }
        $d.= "</table><p>============= NOT DONE LINES ==================</p><table border=1>";
        foreach ($this->getNotDoneLines() as $line) {
            $d.= '<tr><td>#' . $line->getIndex() . '</td><td>' . htmlentities($line->input, ENT_QUOTES) . '</td></tr>';
        }
        $d.= "</table><p>============= LINE BY LINE ==================</p><table border=1>";
        foreach ($this->lexer->getLines() as $line) {
            $d.= '<tr>';
            $d.= '<td>#' . $line->getIndex() . '</td>';
            $d.= '<td>'.htmlentities($line->input, ENT_QUOTES) . '</td>';
            $d.= '<td>'.var_export($line->getIsInline(), true).'</td>';
            $d.= '<td>'.var_export($line->isPicked(), true).'</td>';
            $d.= '<td>'.var_export($line->getAttributes(), true).'</td>';
            $d.= '</tr>';
        }
        $d.= '</table>';
        
        return nl2br($d);
    }
}

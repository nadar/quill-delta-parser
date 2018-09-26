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
        $d.= "</table><p>============= LINE BY LINE ==================</p>";

        $d.= $this->getLinesTable();
        
        return nl2br($d);
    }

    public function getLinesTable()
    {
        $lines = [];
        foreach ($this->lexer->getLines() as $line) {
            $lines[] = [
                $line->getIndex(),
                htmlentities($line->input, ENT_QUOTES),
                htmlentities($line->output, ENT_QUOTES),
                htmlentities($line->renderPrepend(), ENT_QUOTES),
                var_export($line->getAttributes(), true),
                var_export($line->getIsInline(), true),
                var_export($line->isPicked(), true),
                var_export($line->hasEndNewline(), true),
                var_export($line->hasNewline(), true),
                var_export($line->isEmpty(), true),
            ];
        }

        return $this->renderTable($lines, ['ID', 'input', 'output', 'prepend', 'attributes', 'is inline', 'is picked', 'has end newline', 'has new line', 'is empty']);
    }

    protected function renderTable(array $rows, array $head = [])
    {
        $buffer = '<table border="1" width="100%" cellpadding="3" cellspacing="0">';
        
        if (!empty($head)) {
            $buffer.= '<thead><tr>';
            foreach ($head as $col) {
                $buffer.= '<td><b>'.$col.'</b></td>';
            }
            $buffer.= '</tr></thead>';
        }

        foreach ($rows as $cols) {
            $buffer .= '<tr onclick="this.style.backgroundColor= \'red\'">';
            foreach ($cols as $col) {
                $buffer .= '<td>'.$col.'</td>';
            }
            $buffer .= '</tr>';
        }
        return $buffer . '</table>';
    }
}

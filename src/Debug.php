<?php

namespace nadar\quill;

/**
 * Debug Object.
 *
 * The Debug class can return informations in a readable way from a lexer object. It will generate a html table
 * with additional infos about how each line is parsed line by line.
 *
 * ```php
 * $lexer = new Lexer($json);
 * $lexer->render(); // make sure to run the render before call debugPrint();
 *
 * $debug = new Debug($lexer);
 * echo $debug->debugPrint();
 * ```
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Debug
{
    /**
     * @var Lexer
     */
    public $lexer;

    /**
     * Debug constructor
     *
     * @param Lexer $lexer
     */
    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * Get an array of lines which does not have status done.
     *
     * @return array
     */
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

    /**
     * Get an array of lines which does not have the status picked
     *
     * @return array
     */
    protected function getNotPickedLines()
    {
        $lines = [];
        foreach ($this->lexer->getLines() as $line) {
            if (!$line->isPicked() && !$line->isDone()) {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    /**
     * return a string with debug informations.
     *
     * @return string
     */
    public function debugPrint()
    {
        $d = "<p><b>NOT PICKED LINES</b></p>";
        $d.= $this->getLinesTable($this->getNotPickedLines());

        $d.= "<p><b>NOT DONE LINES</b></p>";
        $d.= $this->getLinesTable($this->getNotDoneLines());
        
        $d.= "<p><b>LINE BY LINE</b></p>";
        $d.= $this->getLinesTable($this->lexer->getLines());
        
        return nl2br($d);
    }

    /**
     * Get an array with alles lines rendered a string
     *
     * @param array $lines
     * @return string
     */
    public function getLinesTable(array $lines)
    {
        $_lines = [];
        foreach ($lines as $line) {
            $_lines[] = [
                $line->getIndex(),
                $line->getInput(),
                htmlentities($line->output, ENT_QUOTES),
                htmlentities($line->renderPrepend(), ENT_QUOTES),
                implode(", ", $line->getDebugInfo()),
                var_export($line->getAttributes(), true),
                var_export($line->isInline(), true),
                $this->lineStatus($line),
                var_export($line->hasEndNewline(), true),
                var_export($line->hasNewline(), true),
                var_export($line->isEmpty(), true),
            ];
        }

        return $this->renderTable($_lines, ['ID', 'input', 'output', 'prepend', 'debug', 'attributes', 'is inline', 'status', 'has end newline', 'has new line', 'is empty']);
    }

    /**
     * Get the status waterfall of a given line
     *
     * @param Line $line
     * @return string
     */
    public function lineStatus(Line $line)
    {
        if ($line->isDone()) {
            return 'Picked => Done';
        } elseif ($line->isPicked()) {
            return 'Picked';
        }

        return '';
    }

    /**
     * Render the given table line by line
     *
     * @param array $rows
     * @param array $head
     * @return string
     */
    protected function renderTable(array $rows, array $head = [])
    {
        $buffer = '<table class="table table-bordered table-striped table-hover table-sm small" border="1" width="100%" cellpadding="3" cellspacing="0">';
        
        if (!empty($head)) {
            $buffer.= '<thead><tr>';
            foreach ($head as $col) {
                $buffer.= '<td><b>'.$col.'</b></td>';
            }
            $buffer.= '</tr></thead>';
        }

        foreach ($rows as $cols) {
            $buffer .= '<tr onclick="this.style.backgroundColor=(this.style.backgroundColor==\'red\')?(\'transparent\'):(\'red\');">';
            foreach ($cols as $col) {
                $buffer .= '<td>'.$col.'</td>';
            }
            $buffer .= '</tr>';
        }

        return $buffer . '</table>';
    }
}

<?php

namespace nadar\quill\listener;

use nadar\quill\BlockListener;
use nadar\quill\Lexer;
use nadar\quill\Line;
use nadar\quill\Pick;

/**
 * Convert Table elements into HTML table.
 *
 * Supports Quill table modules including quill-better-table which use
 * the table attribute format on newlines.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Table extends BlockListener
{
    /**
     * @var string
     */
    public const ATTRIBUTE_TABLE = 'table';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $tableRowId = $line->getAttribute(self::ATTRIBUTE_TABLE);
        if ($tableRowId) {
            $this->pick($line, ['rowId' => $tableRowId]);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        $tables = [];
        
        // Group cells by row ID
        foreach ($this->picks() as $pick) {
            $first = $this->getFirstLine($pick);
            
            // Get the cell content
            $buffer = null;
            $first->while(static function (&$index, Line $line) use (&$buffer, $pick, $first) {
                ++$index;
                $buffer .= $line->getInput();
                $line->setDone();
                if ($index == $pick->line->getIndex() || $first->getIndex() === $pick->line->getIndex()) {
                    return false;
                }
            });
            
            $rowId = $pick->optionValue('rowId');
            
            if (!isset($tables[$rowId])) {
                $tables[$rowId] = [];
            }
            
            // Add cell to row in order
            $tables[$rowId][] = $buffer;
        }
        
        // Build the table HTML
        $output = '<table>' . PHP_EOL;
        $output .= '<tbody>' . PHP_EOL;
        
        foreach ($tables as $rowId => $cells) {
            $output .= '<tr>' . PHP_EOL;
            
            foreach ($cells as $cellContent) {
                $output .= '<td>' . $cellContent . '</td>' . PHP_EOL;
            }
            
            $output .= '</tr>' . PHP_EOL;
        }
        
        $output .= '</tbody>' . PHP_EOL;
        $output .= '</table>' . PHP_EOL;
        
        // Set output on the last pick
        $picks = $this->picks();
        if (!empty($picks)) {
            $lastPick = end($picks);
            $lastPick->line->output = $output;
            $lastPick->line->setDone();
        }
    }
}

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
 * the table-cell-line attribute format.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Table extends BlockListener
{
    /**
     * @var string
     */
    public const ATTRIBUTE_TABLE_CELL_LINE = 'table-cell-line';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $tableCellLine = $line->getAttribute(self::ATTRIBUTE_TABLE_CELL_LINE);
        if ($tableCellLine) {
            $this->pick($line, ['cellData' => $tableCellLine]);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        $tables = [];
        
        // Group cells by row
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
            
            $cellData = $pick->optionValue('cellData');
            $rowId = $cellData['row'] ?? '';
            $cellId = $cellData['cell'] ?? '';
            $rowspan = $cellData['rowspan'] ?? 1;
            $colspan = $cellData['colspan'] ?? 1;
            
            if (!isset($tables[$rowId])) {
                $tables[$rowId] = [];
            }
            
            $tables[$rowId][$cellId] = [
                'content' => $buffer,
                'rowspan' => $rowspan,
                'colspan' => $colspan
            ];
        }
        
        // Build the table HTML
        $output = '<table>' . PHP_EOL;
        
        foreach ($tables as $rowId => $cells) {
            $output .= '<tr>';
            
            // Sort cells by cell ID to maintain order
            ksort($cells);
            
            foreach ($cells as $cellId => $cellInfo) {
                $output .= '<td';
                
                if ($cellInfo['rowspan'] > 1) {
                    $output .= ' rowspan="' . $cellInfo['rowspan'] . '"';
                }
                
                if ($cellInfo['colspan'] > 1) {
                    $output .= ' colspan="' . $cellInfo['colspan'] . '"';
                }
                
                $output .= '>' . $cellInfo['content'] . '</td>';
            }
            
            $output .= '</tr>' . PHP_EOL;
        }
        
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

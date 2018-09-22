<?php

namespace nadar\quill\listener;

use nadar\quill\Delta;
use nadar\quill\Listener;
use nadar\quill\Parser;
use nadar\quill\Line;
use nadar\quill\Lexer;

class Text extends Listener
{
    public function priority(): int
    {
        return self::PRIORITY_GARBAGE_COLLECTOR;
    }

    public function type(): int
    {
        return self::TYPE_BLOCK;
    }

    public function process(Line $line)
    {
        if (!$line->previous()) {
            $this->pick($line, ['isFirst' => true]);
        } else {
            $this->pick($line, ['isFirst' => false]);
        }
    }

    private function appendDebug(Lexer $lexer, Line $line, $char)
    {
        if ($lexer->debug) {
            return '<!--[' . $line->row . '=>'.$char . ']-->';
        }
    }

    public function render(Lexer $lexer)
    {
        $isOpen = false;
        foreach ($this->picks() as $pick) {
            if (!$pick->line->isDone() && !$pick->line->hasAttribute()) {
                $output = [];

                // quick access and mark as done
                $line = $pick->line;
                $line->setDone();

                // short form
                $next = $pick->line->next();
                $prev = $pick->line->previous();

                // wenn previous ist inline und das tag bereits göffnet. Am schluss schliesen.
                // aber nur wenn diese linie leer ist.
                if ($isOpen && ($prev && $prev->isInline) && $line->isEmpty()) {
                    $output[] = '</p>' . $this->appendDebug($lexer, $line, 'A');
                    $isOpen = false;
                }
                
                // if its close - we just open tag paragraph as we have a line here!
                if (!$isOpen) {
                    $output[] = '<p>' . $this->appendDebug($lexer, $line, 'B');
                    $isOpen = true;
                }

                // write the actuall content of the element into the output
                $output[] = $line->isEmpty() ? '<br>' : $line->input;

                // if its open and we have a next element, and the next element is not an inline, we close!
                if ($isOpen && ($next && !$next->isInline)) {
                    $output[] = '</p>' . $this->appendDebug($lexer, $line, 'C');
                    $isOpen = false;

                // if its open and we dont have a next element, its the end of the document! lets close this damn paragraph.
                } elseif ($isOpen && !$next) {
                    $output[] = '</p>' . $this->appendDebug($lexer, $line, 'D');
                    $isOpen = false;

                // its open, but the previous element was already an inline element, so maybe we should close and the next element
                // will take care of the "situation".
                } elseif ($isOpen && ($prev && $prev->isInline)) {
                    $output[] = '</p>' . $this->appendDebug($lexer, $line, 'X');
                    $isOpen = false;
            
                // If this element is empty we should maybe directly close and reopen this paragraph as it could be an empty line with
                // a next elmenet
                } elseif ($line->isEmpty() && $next) {
                    $output[] = '</p><p>' . $this->appendDebug($lexer, $line, 'Y');
                    $isOpen = true;
                }
                
                // we have a next element and the next elmenet is inline and its not open, open ...!
                if ($next && $next->isInline && !$isOpen) {
                    $output[] = '<p>' . $this->appendDebug($lexer, $line, 'E');
                    $isOpen = true;
                }

                $pick->line->output = implode("", $output);
            }
        }
    }
}

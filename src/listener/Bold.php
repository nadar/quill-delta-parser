<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;
use nadar\quill\Lexer;

class Bold extends InlineListener
{
    public function process(Line $line)
    {
        if ($line->getAttribute('bold')) {
            $this->updateInput($line, '<strong>'.$line->input.'</strong>');
            //$this->inlinePick($line, '<strong>'.$line->input.'</strong>');
        }
    }
    /*
    public function process(Line $line)
    {
        if ($line->getAttribute('bold')) {
            $this->pick($line);
            $line->setAsInline();
        }
    }

    public function render(Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            $this->updateInput($pick->line, '<strong>' . $pick->line->input . '</strong>');
        }   
    }
    */
}

<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;
use nadar\quill\Lexer;

class Link extends InlineListener
{
    public function process(Line $line)
    {
        if ($line->getAttribute('link')) {
            //$this->pick($line);
            //$line->setAsInline();
            $link = $line->getAttribute('link');
            $this->updateInput($line, '<a href="'.$link.'" target="_blank">'.$line->input.'</a>');
            //$this->updateInput($line, '')
        }
    }

    /*
    public function render(Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            $link = $pick->line->getAttribute('link');
            $this->updateInput($pick->line, '<a href="'.$link.'" target="_blank">'.$pick->line->input.'</a>');
        }   
    }
    */
}

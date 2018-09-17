<?php

namespace nadar\quill\listener;

use nadar\quill\Delta;
use nadar\quill\Listener;
use nadar\quill\Parser;


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

    public function process(Delta $delta)
    {
        $this->addToBag($delta, false);
        
    }

    public function render(\nadar\quill\Parser $parser)
    {
        $isOpen = false;
        foreach ($this->getBag() as $delta) {

            // DEBUG
            // $delta->debugPrint('text');
       

            $prev = $delta->getPreviousDelta();
            // stage 1
            // there is no previous tag, and its not marked as done (heading, or others), so open!
            if (!$isOpen && !$prev && !$delta->isDone()) {
                $delta->setInsert('<p>'.$this->replaceNewlineInTextNotEnd($delta));
                $isOpen = true;

            // stage 2
            // There is a previous element, and the previous element has an empty endling line, lets open the p tag:
            } elseif (!$isOpen && $prev && $prev->isEmptyNewLine()) {
                $delta->setInsert('<p>'.$this->replaceNewlineInTextNotEnd($delta));
                $isOpen = true;
            }

            // stage 3
            // there is already an open p tag, and the current delta has an empty newline is an empty new line tag
            if ($isOpen && ($delta->isEmptyNewLine() || $delta->hasEndNewLine())) {
                $delta->setInsert($this->replaceNewlineInTextNotEnd($delta) . '</p>');
                $isOpen = false;
            }
        }
    }
    
    private function replaceNewlineInTextNotEnd(Delta $delta)
    {
        $text = $delta->getInsert();
        if ($delta->hasEndNewLine()) {
            $text = substr($text, 0, -1);
        }
        $text = str_replace([PHP_EOL, '\n', '\r'], '</p><p>', $text);

        // if there are <p></p> they should have a br inside
        $text = str_replace("<p></p>", "<p><br></p>", $text);

        // if there is a end new line, we have to re add this information again, otheriwse the stage 3 check wont work!
        if ($delta->hasEndNewLine()) {
            $text .= PHP_EOL;
        }

        return $text;
    }
}
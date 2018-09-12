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
        if ($delta->isEndOfLine() && !$delta->isDone()) {

            $while = true;
            $prev = $delta;
            $text[] = $delta->getInsert();

            while ($while) {

                $prev = $prev->getPreviousDelta();

                if (!$prev || $prev->isEndOfLine() || $prev->isDone()) {
                    $while = false;
                } else {
                    $text[] = $prev->getInsert();
                }
            }
            $content = implode("", array_reverse($text));

            if (substr($content, -1) == PHP_EOL) {
                $content = substr($content, 0, -1);
            }
            $content = str_replace(['\n', PHP_EOL], '</p><p>', $content);
            $content = '<p>'.$content.'</p>';
            
            $content = str_replace('<p></p>', '<p><br></p>', $content);
            
            // remove empty newslines at end of string:
            
            $delta->getParser()->writeBuffer($content);
        }
    }

    public function render(Parser $parser)
    {
        
    }
}
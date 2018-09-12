<?php

namespace nadar\quill\listener;

use nadar\quill\Delta;
use nadar\quill\Listener;


class Text extends Listener
{
    public function type(): int
    {
        return self::TYPE_BLOCK;
    }
    public function render(Delta $delta)
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

            $delta->getParser()->writeBuffer('<p>'.implode("", array_reverse($text)).'</p>');
        }
    }
}
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
            $this->addToBag($delta);

            while ($while) {

                $prev = $prev->getPreviousDelta();

                if (!$prev || $prev->isEndOfLine() || $prev->isDone()) {
                    $while = false;
                } else {
                    $this->addToBag($prev);
                }
            }
        }
    }

    public function render(\nadar\quill\Parser $parser)
    {
        foreach ($this->getBag() as $delta) {
            $value = $delta->getInsert();
            var_dump($value, $delta->isDone());
        }
    }
}
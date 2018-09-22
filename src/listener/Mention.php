<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;
use nadar\quill\Lexer;

/**
 * Mention Quill Plugin Listener.
 * 
 * Example mention insert text:
 * 
 * ```json
 * {"insert":{"mention":{"id":"1","value":"Basil","denotationChar":"@"}}},{"insert":" \n"}
 * ```
 */
class Mention extends InlineListener
{
    public function process(Line $line)
    {
        if ($line->isJsonInsert()) {
            $array = $line->getArrayInsert();
            if (isset($array['mention'])) {
                $this->pick($line);
                $line->setAsInline();
            }
        }
    }

    public function render(Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            $this->updateInput($pick->line, $pick->line->getArrayInsert()['mention']['value']);
        }
    }
}

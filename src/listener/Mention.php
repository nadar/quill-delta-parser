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
                // $this->updateInput($line, $array['mention']['value']);
                //$line->input = $array['mention']['value'];
                $line->setDone();
                //$this->pick($line, ['value' => $array['mention']['value']]);
            }
        }
    }
}

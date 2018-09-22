<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

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
            $line->setAsInline();
            $line->setDone();
            $line->output = 'aa';
            $line->input = 'bb';
        }
    }
}

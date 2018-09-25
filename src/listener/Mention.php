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
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Mention extends InlineListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if ($line->isJsonInsert()) {
            $array = $line->getArrayInsert();
            if (isset($array['mention'])) {
                $line->output = $array['mention']['value'];
                $line->setAsInline();
                $line->setDone();
            }
        }
    }
}

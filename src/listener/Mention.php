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
        // @todo use: $line->insertJsonKey('mention');
        if ($line->isJsonInsert()) {
            $array = $line->getArrayInsert();
            if (isset($array['mention'])) {
                $this->updateInput($line, $array['mention']['value']);
            }
        }
    }
}

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
    public function process(Line $line, Lexer $lexer=null)
    {
        $mention = $line->insertJsonKey('mention');

        if ($mention) {
            $value = ($lexer->escapeInput) ? self::escape($mention['value']) : $mention['value'];
            $this->updateInput($line, $value);
        }
    }
}

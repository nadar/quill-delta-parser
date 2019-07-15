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
    const ATTRIBUTE_NAME = 'mention';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $mention = $line->insertJsonKey(self::ATTRIBUTE_NAME);

        if ($mention) {
            $this->updateInput($line, $line->getLexer()->escape($mention['value']));
        }
    }
}

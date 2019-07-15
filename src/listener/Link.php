<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;
use nadar\quill\Lexer;

/**
 * Convert links into a inline elements.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Link extends InlineListener
{
    const ATTRIBUTE_NAME = 'link';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $link = $line->getAttribute(self::ATTRIBUTE_NAME);
        if ($link) {
            $this->updateInput($line, '<a href="'.$line->getLexer()->escape($link).'" target="_blank">'.$line->getInput().'</a>');
        }
    }
}

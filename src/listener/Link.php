<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;

/**
 * Convert links into a inline elements.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Link extends InlineListener
{
    public $wrapperOpen = '<a href="{link}" target="_blank">';
    public $wrapperMiddle = '{text}';
    public $wrapperClose = '</a>';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line) {
        $link = $line->getAttribute('link');
        if ($link) {
            $wrapper         = '';
            $searchArgument  = [];
            $replaceArgument = [];

            if (($line->previous() !== false) && $line->previous()->getAttribute('link') === $link) {
                $wrapper           .= $this->wrapperOpen;
                $searchArgument[]  = '{link}';
                $replaceArgument[] = $line->getLexer()->escape($link);
            }

            $wrapper           .= $this->wrapperMiddle;
            $searchArgument[]  = '{text}';
            $replaceArgument[] = $line->getInput();

            if (($line->next() !== false) && $line->next()->getAttribute('link') === $link) {
                $wrapper .= $this->wrapperClose;
            }

            $this->updateInput($line, str_replace($searchArgument, $replaceArgument, $wrapper));
        }
    }
}

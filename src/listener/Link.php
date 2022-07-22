<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

/**
 * Convert links into a inline elements.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Link extends InlineListener
{
    /**
     * @var string The wrapper template which is used to generate the link tag
     * @since 3.0
     */
    public $wrapperOpen = '<a href="{link}" target="_blank">';

    /**
     * @var string The content element in between
     * @since 3.0
     */
    public $wrapperMiddle = '{text}';

    /**
     * @var string Closing Tag
     * @since 3.0
     */
    public $wrapperClose = '</a>';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $link = $line->getAttribute('link');
        if ($link) {
            $wrapper = '';
            $searchArgument = [];
            $replaceArgument = [];

            $previousLineHasSimilarLink = $line->previous() !== false && $line->previous()->getAttribute('link') === $link;
            if ($previousLineHasSimilarLink === false) {
                $wrapper .= $this->wrapperOpen;
                $searchArgument[] = '{link}';
                $replaceArgument[] = $line->getLexer()->escape($link);
            }

            $wrapper .= $this->wrapperMiddle;
            $searchArgument[] = '{text}';
            $replaceArgument[] = $line->getInput();

            $nextLineHasSimilarLink = $line->next() !== false && $line->next()->getAttribute('link') === $link;
            if ($nextLineHasSimilarLink === false) {
                $wrapper .= $this->wrapperClose;
            }

            $this->updateInput($line, str_replace($searchArgument, $replaceArgument, $wrapper));
        }
    }
}

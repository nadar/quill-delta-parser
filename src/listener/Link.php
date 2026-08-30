<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;
use nadar\quill\Uri;

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
     * @since 3.0.0
     */
    public $wrapperOpen = '<a href="{link}" target="_blank">';

    /**
     * @var array<string> The list of allowed URI schemes for the `href` attribute. Values with a
     * scheme which is not part of this allowlist (like `javascript:`) are neutralized and point
     * to `#` instead, otherwise this would allow cross-site scripting (XSS). URIs without any
     * scheme (relative paths, anchors) are always allowed and can not be removed from validation.
     * @since 3.6.0
     */
    public $safeSchemes = Uri::SAFE_SCHEMES;

    /**
     * @var string The content element in between
     * @since 3.0.0
     */
    public $wrapperMiddle = '{text}';

    /**
     * @var string Closing Tag
     * @since 3.0.0
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
                $replaceArgument[] = Uri::isSafe($link, $this->safeSchemes) ? $line->getLexer()->escape($link) : '#';
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

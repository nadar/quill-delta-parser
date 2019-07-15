<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\Lexer;
use nadar\quill\BlockListener;

/**
 * Convert Video attributes into tags.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Video extends BlockListener
{
    const ATTRIBUTE_NAME = 'video';

    public $wrapper = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="{url}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $embedUrl = $line->insertJsonKey(self::ATTRIBUTE_NAME);
        if ($embedUrl) {
            $line->output = str_replace(['{url}'], [$line->getLexer()->escape($embedUrl)], $this->wrapper);
            $line->setDone();
        }
    }
}

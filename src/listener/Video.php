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
    public $wrapper = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="{url}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
    /**
     * {@inheritDoc}
     */
    public function process(Line $line, Lexer $lexer=null)
    {
        $embedUrl = $line->insertJsonKey('video');
        if ($embedUrl) {
            $embedUrl = ($lexer->escapeInput) ? self::escape($embedUrl) : $embedUrl;
            $line->output = str_replace(['{url}'], [$embedUrl], $this->wrapper);
            $line->setDone();
        }
    }
}

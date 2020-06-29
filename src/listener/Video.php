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
    /**
     * @var array Allow options for iframe allow param
     * @since 2.5.0
     */
    public $allow = ['accelerometer', 'autoplay', 'encrypted-media', 'gyroscope', 'picture-in-picture'];

    /**
     * @var string The wrapper template which is taken to generate the video element.
     */
    public $wrapper = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="{url}" frameborder="0" allow="{allow}" allowfullscreen></iframe></div>';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $embedUrl = $line->insertJsonKey('video');
        if ($embedUrl) {
            $line->output = str_replace(['{url}', '{allow}'], [$line->getLexer()->escape($embedUrl), implode("; ", $this->allow)], $this->wrapper);
            $line->setDone();
        }
    }
}

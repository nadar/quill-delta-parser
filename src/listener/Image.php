<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

/**
 * Convert Image attributes into image element.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.2
 */
class Image extends InlineListener
{
    /**
     * @var string
     */
    public $wrapper = '<img src="{src}" {width} {height} alt="" class="img-responsive img-fluid" />';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $embedUrl = $line->insertJsonKey('image');
        if ($embedUrl) {
            if ($width = $line->getAttribute('width')) {
                $width = 'width="'.$line->getLexer()->escape($width).'"';
            }

            if ($height = $line->getAttribute('height')) {
                $height = 'height="'.$line->getLexer()->escape($height).'"';
            }

            $this->updateInput($line, preg_replace('/\s+/', ' ', str_replace([
                '{src}',
                '{width}',
                '{height}'
            ], [
                $line->getLexer()->escape($embedUrl),
                $width,
                $height
            ], $this->wrapper)));
        }
    }
}

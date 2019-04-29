<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\Lexer;
use nadar\quill\BlockListener;
use nadar\quill\InlineListener;

/**
 * Convert Image attributes into image element.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.2
 */
class Image extends InlineListener
{
    public $wrapper = '<img src="{src}" alt="" class="img-responsive img-fluid" />';
    
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $embedUrl = $line->insertJsonKey('image');
        if ($embedUrl) {
            $this->updateInput($line, str_replace(['{src}'], [$line->getLexer()->escape($embedUrl)], $this->wrapper));
        }
    }
}

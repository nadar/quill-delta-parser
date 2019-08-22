<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

/**
 * Fonts attribute
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 2.1.0
 */
class Font extends InlineListener
{
    /**
     * @var boolean If ignore is enabled, the font won't apply. This can be use full if font styles is disabled in your quill editor
     * but people copy past content from somewhere else which will then generate the font attribute.
     */
    public $ignore = false;

    public function process(Line $line)
    {
        if (($font = $line->getAttribute('font'))) {
            $this->updateInput($line, $this->applyTemplate($font, $line));
        }
    }

    public function applyTemplate($font, Line $line)
    {
        if ($this->ignore) {
            return $line->input;
        }

        return '<span style="font-family: '.$line->getLexer()->escape($font).';">'. $line->input . '</span>';
    }
}
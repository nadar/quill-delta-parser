<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

/**
 * Renders font attribute which will set font-family.
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

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if (($font = $line->getAttribute('font'))) {
            $this->updateInput($line, $this->applyTemplate($font, $line));
        }
    }

    /**
     * Wrap the font family span tag if ignore is disabled.
     *
     * @param string $font
     * @return string
     */
    public function applyTemplate($font, Line $line)
    {
        if ($this->ignore) {
            return $line->getInput();
        }

        return '<span style="font-family: '.$line->getLexer()->escape($font).';">'. $line->getInput() . '</span>';
    }
}

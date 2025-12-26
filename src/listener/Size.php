<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

/**
 * Convert size attributes into span tag with font-size style.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 3.5.0
 */
class Size extends InlineListener
{
    /**
     * @var boolean If ignore is enabled, the size won't apply. This can be useful if font size is disabled in your quill editor
     * but people copy paste content from somewhere else which will then generate the size attribute.
     */
    public $ignore = false;

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if (($size = $line->getAttribute('size'))) {
            $this->updateInput($line, $this->ignore ? $line->getInput() : '<span style="font-size:'.$line->getLexer()->escape($size).'">'.$line->getInput().'</span>');
        }
    }
}

<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;

/**
 * Convert color attributes into span tag.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.1.0
 */
class Color extends InlineListener
{
    /**
     * @var boolean If ignore is enabled, the colors won't apply. This can be use full if coloring is disabled in your quill editor
     * but people copy past content from somewhere else which will then generate the color attribute.
     */
    public $ignore = false;
    
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if (($color = $line->getAttribute('color'))) {
            $this->updateInput($line, $this->ignore ? $line->input : '<span style="color:'.$line->getLexer()->escape($color).'">'.$line->getInput().'</span>');
        }
    }
}

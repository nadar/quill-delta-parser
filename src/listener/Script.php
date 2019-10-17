<?php

namespace nadar\quill\listener;

use nadar\quill\InlineListener;
use nadar\quill\Line;

/**
 * Renders script attribute which will generate sup/sub tags.
 * 
 * @author Gaëtan Faugère <gaetan@fauge.re>
 * @since 2.1.2
 */
class Script extends InlineListener
{

    public $scriptTags = ['super', 'sub'];

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if (($script = $line->getAttribute('script'))) {
            $this->updateInput($line, $this->applyTemplate($script, $line));
        }
    }

    /**
     * Wrap in sup/sub tag.
     *
     * @param string $script
     * @param Line $line
     * @return string
     */
    public function applyTemplate($script, Line $line)
    {
        if (!in_array($script, $this->scriptTags)) {
            // prevent html injection and wrong behaviors
            throw new Exception('An unknown script tag "' . $script . '" has been detected.');
        }
        if ($script === 'super') {
          $tag = 'sup';
        } else if ($script === 'sub') {
          $tag = 'sub';
        }
        return '<'.$tag.'>'. $line->input . '</'.$tag.'>';
    }
}

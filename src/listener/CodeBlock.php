<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\BlockListener;
use nadar\quill\Lexer;

/**
 * Code Block
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 2.7.0
 */
class CodeBlock extends BlockListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $heading = $line->getAttribute('code-block');
        if ($heading) {
            $this->pick($line);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        $this->wrapElement('<pre><code>{__buffer__}</code></pre>');
    }
}

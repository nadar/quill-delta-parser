<?php

namespace nadar\quill\options;

use nadar\quill\Line;
use nadar\quill\Pick;

trait AlignTrait
{
    /**
     * @var array Supported alignments.
     */
    public $alignments = ['center', 'right', 'justify'];

    public function getAlignOption(Line $line)
    {
        return $line->getAttribute('align');
    }

    public function alignValue($value, Pick $pick, $name)
    {
        $v = $pick->align;

        if (empty($v)) {
            return '';
        }

        if ($name == 'alignClose') {
            return $this->alignClose();
        }

        return $this->alignOpen($v);
    }

    public function alignOpen($name)
    {
        return "<span style=\"text-align: {$name};\">";
    }

    public function alignClose()
    {
        return '</span>';
    }
}
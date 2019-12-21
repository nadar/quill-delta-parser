<?php

namespace nadar\quill\options;

use nadar\quill\Line;
use nadar\quill\Pick;

trait AlignTrait
{
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
            return "</{$v}>";
        }

        return "<$v>";
    }
}
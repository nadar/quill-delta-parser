<?php

namespace nadar\quill\options;

use nadar\quill\Line;

trait AlignTrait
{
    public function getAlignOption(Line $line)
    {
        return $line->getAttribute('align');
    }

    public function alignValue($value)
    {
        // @TODO: How to close the attribute afterwards?
        // @TODO use inline span for certain attributes like justify
        return '<'.$value.'>';
    }
}
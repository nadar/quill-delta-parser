<?php

namespace nadar\quill;

class Pick
{
    public $options = [];
    public $line;
    
    public function __construct(Line $line, array $options = [])
    {
        $this->line = $line;
        $this->options = $options;
    }

    public function __get($name)
    {
        return $this->options[$name];
    }
}

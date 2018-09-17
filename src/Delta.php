<?php

namespace nadar\quill;

class Delta
{
    protected $delta = [];

    protected $parser;

    protected $key;

    protected $done = false;

    public function __construct(array $delta, int $key, Parser $parser)
    {
        $this->delta = $delta;
        $this->parser = $parser;
        $this->key = $key;
    }

    public function remove()
    {
        $this->parser->removeDelta($this->key);
    }

    public function setDone()
    {
        $this->done = true;
    }

    public function isDone()
    {
        return $this->done;
    }

    protected function getDeltaByKey($key, $default = false)
    {
        return array_key_exists($key, $this->delta) ? $this->delta[$key] : $default;
    }

    protected function setDeltaByKey($key, $value)
    {
        $this->delta[$key] = $value;
    }

    public function getParser()
    {
        return $this->parser;
    }

    public function getInsert()
    {
        return $this->getDeltaByKey('insert');
    }

    public function setInsert($insert)
    {
        $this->setDeltaByKey('insert', $insert);
    }

    public function getAttributes()
    {
        return $this->getDeltaByKey('attributes', []);
    }

    public function getAttribute($name)
    {
        return array_key_exists($name, $this->getAttributes()) ? $this->getAttributes()[$name] : false;
    }

    public function getPreviousDelta()
    {
        if ($this->key == 0) {
            return false;
        }
        return $this->getParser()->getDelta($this->key - 1);
    }

    public function isEndOfLine()
    {
        $chars = [
            PHP_EOL, '\n', '\r',
        ];
        
        return in_array(substr($this->getInsert(), -1), $chars);
    }
}
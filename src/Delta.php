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

    public function hasEndNewLine()
    {
        $chars = [
            PHP_EOL, '\n', '\r',
        ];
        
        return in_array(substr($this->getInsert(), -1), $chars);
    }

    public function isEmptyNewLine()
    {
        $chars = [
            PHP_EOL, '\n', '\r',
        ];
        return in_array($this->getInsert(), $chars);
    }

    public function debugPrint($context)
    {
        echo PHP_EOL . '----------> ' . $context . PHP_EOL . '#' . $this->key . ':' . PHP_EOL;
        echo 'insert: ' . var_export($this->getInsert(), true) . PHP_EOL;
        echo 'insert: (nl2br) ' . var_export(nl2br($this->getInsert()), true) . PHP_EOL;
        echo 'isDone: ' . var_export($this->isDone(), true) . PHP_EOL;
        echo 'isEmptyNewLine: ' . var_export($this->isEmptyNewLine(), true) . PHP_EOL;
        echo 'hasEndNewLine: ' . var_export($this->hasEndNewLine(), true) . PHP_EOL;
        echo '----------' . PHP_EOL . PHP_EOL . PHP_EOL;
    }
}
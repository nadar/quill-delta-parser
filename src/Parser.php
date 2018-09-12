<?php

namespace nadar\quill;

class Parser
{
    protected $json;

    protected $deltas = [];

    protected $listeners = [
        Listener::TYPE_BLOCK => [],
        Listener::TYPE_INLINE => [],
    ];

    public function __construct(string $json)
    {
        $this->json = $json;
    }

    public function registerListener(Listener $listener)
    {
        $this->listeners[$listener->type()][] = $listener;
    }

    public function getJsonArray() : array
    {
        return json_decode($this->json, true);
    }

    public function getOps() : array
    {
        return isset($this->getJsonArray()['ops']) ? $this->getJsonArray()['ops'] : $this->getJsonArray();
    }

    public function render() : string
    {
        foreach ($this->getOps() as $key => $delta) {
            $delta = $this->setDelta($key, new Delta($delta, $key, $this));
            foreach ($this->listeners[Listener::TYPE_INLINE] as $listener) {
                $listener->render($delta);
            }
            foreach ($this->listeners[Listener::TYPE_BLOCK] as $listener) {
                $listener->render($delta);
            }
        }
        
        return implode(PHP_EOL, $this->buffer);
    }

    public function getDelta($key)
    {
        return isset($this->deltas[$key]) ? $this->deltas[$key] : false;
    }

    public function setDelta($key, Delta $delta)
    {
        $this->deltas[$key] = $delta;
        return $delta;
    }

    protected $buffer = [];

    public function writeBuffer($string)
    {
        $this->buffer[] = $string;
    }
}
<?php

namespace nadar\quill;

use nadar\quill\listener\Bold;
use nadar\quill\listener\Italic;
use nadar\quill\listener\Heading;
use nadar\quill\listener\Text;
use nadar\quill\listener\Lists;


class Parser
{
    protected $json;

    protected $deltas = [];

    protected $listeners = [
        Listener::TYPE_INLINE => [
            Listener::PRIORITY_EARLY_BIRD => [],
            Listener::PRIORITY_GARBAGE_COLLECTOR => [],
        ],
        Listener::TYPE_BLOCK => [
            Listener::PRIORITY_EARLY_BIRD => [],
            Listener::PRIORITY_GARBAGE_COLLECTOR => [],
        ],
    ];

    public function __construct(string $json)
    {
        $this->json = $json;
    }

    public function initBuiltInListeners()
    {
        $this->registerListener(New Bold);
        $this->registerListener(new Italic);
        $this->registerListener(new Heading);
        $this->registerListener(new Text);
        $this->registerListener(new Lists);
    }

    public function registerListener(Listener $listener)
    {
        $this->listeners[$listener->type()][$listener->priority()][] = $listener;
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
            foreach ($this->listeners[Listener::TYPE_INLINE] as $prios) {
                foreach ($prios as $listener) {
                    $listener->process($delta);
                }
            }
            foreach ($this->listeners[Listener::TYPE_BLOCK] as $prios) {
                foreach ($prios as $listener) {
                    $listener->process($delta);
                }
            }
        }
        
        foreach ($this->listeners[Listener::TYPE_BLOCK] as $prios) {
            foreach ($prios as $listener) {
                $listener->render($this);
            }
        }

        $buff = null;
        foreach ($this->deltas as $delta) {
            $buff.= $delta->getInsert();
        }
        
        return $this->removeNewlines($buff);
    }

    public function removeNewlines($content)
    {
        return str_replace([
            PHP_EOL, '\n', '\r',
        ], '', $content);
    }

    public function setDelta($key, Delta $delta)
    {
        $this->deltas[$key] = $delta;
        return $delta;
    }

    public function getDelta($key)
    {
        return isset($this->deltas[$key]) ? $this->deltas[$key] : false;
    }

    public function removeDelta($key)
    {
        unset($this->deltas[$key]);
    }
}
<?php

namespace nadar\quill;

class Lexer
{
    const NEWLINE_EXPRESSION = '<!--CDATA:NEWLINE-->';
    protected $json;

    public function __construct(string $json)
    {
        $this->json = $json;
    }

    public function getJsonArray() : array
    {
        return json_decode($this->json, true);
    }

    public function getOps() : array
    {
        return isset($this->getJsonArray()['ops']) ? $this->getJsonArray()['ops'] : $this->getJsonArray();
    }

    protected $strf = [];

    public function arrayify()
    {
        foreach ($this->getOps() as $key => $delta) {
            $insert = str_replace(PHP_EOL, self::NEWLINE_EXPRESSION, $delta['insert']);
            $this->strf[] = ['string' => $insert, 'lines' => explode(self::NEWLINE_EXPRESSION, $insert), 'attribute' => isset($delta['attributes']) ? $delta['attributes'] : []];
            
        }

        return $this->strf;
    }
}
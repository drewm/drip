<?php

namespace DrewM\Drip;

class Dataset implements \JsonSerializable
{
    protected $label = false;
    protected $data  = [];

    public function __construct($label, $data)
    {
        $this->label = $label;
        $this->data  = $data;
    }

    public function jsonSerialize() : array
    {
        return [
            $this->label => [
                $this->data,
            ],
        ];
    }

}
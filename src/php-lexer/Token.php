<?php

namespace phpLexer;

class Token {

    private $data;
    private $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getType()
    {
        return $this->type;
    }

    public function toString()
    {
        return "<" . $this->getData() . ", " . $this->getType() . ">";
    }
}
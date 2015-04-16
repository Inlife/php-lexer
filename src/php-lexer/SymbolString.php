<?php

namespace phpLexer;

class SymbolString {

    private $data;
    private $line;
    private $length;

    public function __construct($string, $line)
    {
        $this->data = str_split($string);
        $this->length = strlen($string);
        $this->line = $line;
    }

    public function each($callback)
    {
        $iteration = 0;
        foreach($this->data as $character) {
            $callback(new Symbol($character, $iteration++, $this));
        }

        $callback(new Symbol(' ', $iteration, $this)); // last symbol
    }

    public function getLine()
    {
        return $this->line;
    }

    public function toString()
    {
        return implode('', $this->data);
    }

    public function getLength()
    {
        return $this->length;
    }
}
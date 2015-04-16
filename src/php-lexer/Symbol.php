<?php

namespace phpLexer;

class Symbol {

    private $data;
    private $iteration;
    private $skipping;
    private $string;

    public function __construct($character, $iteration, $string)
    {
        $this->data = $character;
        $this->iteration = $iteration;
        $this->skipping = false;
        $this->string = $string;

        $this->types = [
            T_SPACE         => function($symbol) { return ($symbol === ' '); },
            T_LETTER        => function($symbol) { return ( ($symbol >= 'a' && $symbol <= 'z') || ($symbol >= 'A' && $symbol <= 'Z') || ($symbol === '_') ); },
            T_NUMBER        => function($symbol) { return ($symbol >= '0' && $symbol <= '9'); },
            T_SPECIAL       => function($symbol) { return in_array($symbol, str_split('$./')); },
            T_OPERATOR      => function($symbol) { return in_array($symbol, str_split('+-/*<>=^!.&|')); },
            T_PUNCTUATION   => function($symbol) { return in_array($symbol, str_split('{}[](),:;')); },
            T_STRING        => function($symbol) { return in_array($symbol, str_split('\'"')); },
        ];
    }
    
    public function is($types, $callback)
    {
        if (gettype($types) != 'array') $types = array($types);

        if (in_array(T_ALL, $types)) {
            $callback($this->iteration, $this);
            return $this;
        }

        if (!$this->skipping) {
            $called = false;
            foreach($types as $type) {
                if ($this->types[$type]($this->data) && !$called) {
                    $callback($this->iteration, $this); $called = true;
                }
            }
        }

        return $this;
    }

    public function isEOL()
    {
        return ($this->getColumn() === $this->getString()->getLength());
    }

    public function getData()
    {
        return $this->data;
    }

    public function skip()
    {
        $this->skipping = true;
    }

    public function like($value)
    {
        return ($this->data === $value);
    }

    public function getString()
    {
        return $this->string;
    }

    public function getColumn()
    {
        return $this->iteration;
    }
}
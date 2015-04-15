<?php

namespace phpLexer;

use Finite\StatefulInterface;

class Lexeme implements StatefulInterface
{
    private $state;

    public function getFiniteState()
    {
        return $this->state;
    }
    
    public function setFiniteState($state)
    {
        $this->state = $state;
    }
}
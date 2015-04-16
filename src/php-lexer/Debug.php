<?php

namespace phpLexer;

class Debug {

    private $level = 1; // 0 - disable, 1 - errors, 2 - debug

    public function __construct($debug)
    {
        $this->level = $debug;
    }

    private function write($data, $urgency = 0)
    {
        if ($this->level === 2 || ($urgency === 1 && $this->level === 1)) {
            print($data);
        }
        return $this;
    }

    private function writeln($data, $urgency = 0)
    {
        return $this->write("$data\n", $urgency);
    }

    private function tab($count = 1, $urgency = 0)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->write("\t", $urgency);
        }

        return $this;
    }

    public function start($iteration, Expression $expression)
    {
        $this
            ->writeln("[$iteration] : {")
            ->tab()->writeln("stateA: " . $expression->getFiniteState())
        ;
    }

    public function finish($iteration, Expression $expression)
    {
        $this
            ->tab()->writeln("stateB: " . $expression->getFiniteState())
            ->writeln("}")
        ;
    }

    public function type($type)
    {
        $this->tab()->write("found $type: ");
    }

    public function value(Symbol $symbol)
    {
        $this->writeln($symbol->getData());
    }

    public function error($data)
    {
        $symbol = end($data[3]);
        $string = $symbol->getString();
        $line = $string->getLine();
        $col = $symbol->getColumn();

        $this
            ->writeln("ERROR: " . $data[0], 1)
                ->tab(1,1)->writeln("current state: " . $data[1]->getFiniteState(), 1)
                ->tab(1,1)->writeln("trying to switch to: " . implode(', ', $data[2]), 1)
                ->tab(1,1)->writeln("at line $line, col: $col", 1)
                ->tab(1,1)->writeln("near: " . $string->toString(), 1)
        ;
    }

    public function token(Token $token)
    {
        $this
            ->tab()
                ->write('token: <')
                ->write($token->getData())
                ->write(', ')
                ->write($token->getType())
                ->writeln('>')
        ;
    }
}
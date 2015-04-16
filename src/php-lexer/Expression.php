<?php

namespace phpLexer;

use Finite\StatefulInterface;
use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;

class Expression implements StatefulInterface
{
    private $state;
    private $data;
    private $sm;
    private $handlers = [];
    private $tokens = [];
    private $symbols = [];

    public function __construct($scheme)
    {
        $this->sm = new StateMachine;

        $loader = new ArrayLoader($scheme);
        $loader->load($this->sm);

        $this->sm->setObject($this);
        $this->reset();
    }

    public function getFiniteState()
    {
        return $this->state;
    }

    public function setFiniteState($state)
    {
        $this->state = $state;
    }

    public function add(Symbol $symbol)
    {
        $this->symbols[] = $symbol;

        if ($this->getFiniteState() !== 'empty') {
            $this->data .= $symbol->getData();
        }
    }

    public function get()
    {
        return $this->data;
    }

    public function complete()
    {
        if ($this->getFiniteState() !== 'empty') {
            $this->generateToken();
        }
    }

    public function transition($available, $completing, $soft = false)
    {
        if (in_array($this->getFiniteState(), $completing)) {
            $this->generateToken();
        }

        $found = false;

        foreach ($available as $transition) {
            if ($this->sm->can($transition) && $found == false) {
                $this->sm->apply($transition);

                $found = true;
            }
        }

        if (!$found && !$soft) {
            $this->call('error', ['Available transition not found.', $this, $available, $this->symbols]);
        }

        return !(!$found && $soft);
    }

    public function onError($handler)
    {
        $this->handlers['error'] = $handler;
    }

    public function onToken($handler)
    {  
        $this->handlers['token'] = $handler;
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    private function reset()
    {
        $this->data = '';
        $this->sm->initialize();
    }

    private function generateToken()
    {
        if ($this->sm->can('confirm')) {
            $this->tokens[] = new Token($this->get(), $this->getFiniteState());
            $this->call('token', end($this->tokens));

            $this->reset();
        } else {
            $this->call('error', ['Can\'t complete token.']);
        }
    }

    private function call($name, $data)
    {
        $this->handlers[$name]($data);

        if ($name === 'error') {
            $this->sm->apply('reject');
        } elseif ($name === 'token') {
            $this->sm->apply('confirm');
        }
    }

}
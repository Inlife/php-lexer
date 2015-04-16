<?php

namespace phpLexer;

define('DBG_NONE',      0);
define('DBG_ERROR',     1);
define('DBG_ALL',       2);

define('T_ALL',         0);
define('T_SPACE',       1);
define('T_LETTER',      3);
define('T_NUMBER',      4);
define('T_OPERATOR',    5);
define('T_PUNCTUATION', 6);
define('T_SPECIAL',     7);

class Lexer
{
    private $tokens;
    private $scheme;
    private $debug = DBG_ALL;

    public function __construct($input) 
    {
        $this->scheme = [
            'class'   => 'Expression',

            'states'  => [
                'empty'         => [ 'type' => 'initial'],
                'correct'       => [ 'type' => 'final'  ],
                'incorrect'     => [ 'type' => 'final'  ],

                'integer'       => [], 
                'double'        => [], 
                'uoperator'     => [], 
                'boperator'     => [], 
                'punctuation'   => [], 
                'identifier'    => [],
                    // sub states
                    'identifier_dollar' => [],
                    'identifier_start'  => [],
            ],

            'transitions' => [
                'integer' => ['from' => ['empty', 'integer'], 'to' => 'integer'],
                'double' => ['from' => ['double'], 'to' => 'double'],
                'double_point' => ['from' => ['integer'], 'to' => 'double'],
                'uoperator' => ['from' => ['empty'], 'to' => 'uoperator'],
                'boperator' => ['from' => ['uoperator'],'to' => 'boperator'],
                // 'toperator' => ['from' => ['boperator'],'to' => 'boperator'],
                'punctuation' => ['from' => ['empty'], 'to' => 'punctuation'],
                'identifier' => ['from' => ['identifier_start', 'identifier'], 'to' => 'identifier'],
                'identifier_start' => ['from' => ['empty', 'identifier_dollar', 'identifier'], 'to' => 'identifier'],
                'identifier_dollar' => ['from' => ['empty', 'identifier_dollar'], 'to' => 'identifier'],

                'confirm'       => ['from' => ['integer', 'double', 'uoperator', 'boperator', 'punctuation', 'identifier', 'variable'], 'to' => 'empty'],
                'reject'        => ['from' => ['integer', 'double', 'uoperator', 'boperator', 'punctuation', 'identifier', 'variable'], 'to' => 'incorrect'],
            ],
        ];

        $this->scan($input);
    }

    public function scan($input) 
    {
        if ( gettype($input) != 'array' ) {
            $input = explode(PHP_EOL, $input);
        }

        $line = 0;
        foreach ($input as $string) {
            $tokens = $this->tokenize( new SymbolString($string, $line++));

            foreach ($tokens as $token) {
                $this->tokens[] = $token;
            }
        }

        return true;
    }

    private function tokenize(SymbolString $string)
    {
        $debug = new Debug($this->debug);
        $expression = new Expression($this->scheme);

        $expression->onError(function($error) use($debug) {
            $debug->error($error);
            return die();
        });

        $expression->onToken(function(Token $token) use($debug) {
            $debug->token($token);
        });

        $string->each(function(Symbol $symbol) use($debug, $expression) {

            $symbol
                ->is(T_ALL, function($iteration) use($debug, $expression) {
                    $debug->start($iteration, $expression);
                })
                ->is(T_SPACE, function() use($debug, $expression) {
                    $debug->type('space or endline');

                    $expression->complete();
                })
                ->is(T_LETTER, function() use($debug, $expression) {
                    $debug->type('letter');

                    $expression->transition(
                        ['identifier_start', 'identifier'],
                        ['uoperator', 'boperator', 'punctuation']
                    );
                })
                ->is(T_NUMBER, function() use($debug, $expression) {
                    $debug->type('number');

                    $expression->transition(
                        ['integer', 'double', 'identifier'],
                        ['uoperator', 'boperator', 'punctuation']
                    );
                })
                ->is(T_SPECIAL, function() use($debug, $expression, $symbol) {
                    $debug->type('special (checking)');

                    if ($symbol->like('.')) {
                        if ($expression->transition(['double_point'], [], true) ) {                        
                            $symbol->skip(); // skip other compares, to the next symbol
                        }
                    } else if ($symbol->like('$')) {
                        $result = $expression->transition(
                            ['identifier_dollar'],
                            ['punctuation', 'uoperator', 'boperator'], false
                        );
                    }
                })
                ->is(T_OPERATOR, function() use($debug, $expression) {
                    $debug->type('operator');

                    $expression->transition(
                        ['uoperator', 'boperator'],
                        ['integer', 'double', 'identifier', 'variable', 'punctuation']
                    );
                })
                ->is(T_PUNCTUATION, function() use($debug, $expression) {
                    $debug->type('punctuation');

                    $expression->transition(
                        ['punctuation'],
                        ['integer', 'double', 'identifier', 'variable', 'punctuation', 'uoperator', 'boperator']
                    );
                })
                ->is(T_ALL, function($iteration) use($debug, $expression, $symbol) {
                    $debug->finish($iteration, $expression);

                    $expression->add($symbol);
                })
            ;

        });

        return $expression->getTokens();
    }

    public function getTokens()
    {
        return $this->tokens;
    }
}
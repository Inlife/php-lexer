<?php

require 'vendor/autoload.php';

use phpLexer\Lexer;
use phpLexer\Lexeme;

$loader = new ArrayLoader([
    'class'   => 'Lexeme',
    'states'  => [
        'empty'     => [ 'type' => StateInterface::TYPE_INITIAL],
        'correct'   => [ 'type' => StateInterface::TYPE_FINAL  ],
        'incorrect' => [ 'type' => StateInterface::TYPE_FINAL  ],

        // 'i2d', -- i to d, if no number after point ( 123. )

        'integer', 'double', 'uoperator', 'boperator', 'punctuation', 'identifier', 'variable'
    ],
    'transitions' => [
        'integer' => ['from' => ['empty'], 'to' => 'integer'],
        'double'  => ['from' => ['integer'], 'to' => 'double'],
        'uoperator'  => ['from' => ['empty'], 'to' => 'uoperator'],
        'boperator'  => ['from' => ['uoperator'], 'to' => 'boperator'],
        'punctuation'  => ['from' => ['empty'], 'to' => 'punctuation'],
        'identifier'  => ['from' => ['empty'], 'to' => 'identifier'],
        'uoperator'  => ['from' => ['empty'], 'to' => 'variable'],

        'confirm'  => ['from' => ['integer', 'double', 'uoperator', 'boperator', 'punctuation', 'identifier', 'variable'], 'to' => 'correct'],
    ],
]);







// $lexer = new Lexer(file_get_contents('code.php'));

// print("\n");

// $token = $lexer->peek();
// while ($token) {
//     print('< ' . $token['value'] . ' , ' . str_replace('phpLexer\Lexer::', '', $lexer->getLiteral($token['type'])) . " >\n");
//     $token = $lexer->peek();
// }

// print("\n");
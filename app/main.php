<?php

require 'vendor/autoload.php';

use phpLexer\Lexer;


// $lexer = new Lexer(array(
//     '1123 // asd *',
//     '12331'
// ), DBG_ALL);

$lexer = new Lexer(file_get_contents('app/code.php'));

// print_r($lexer->getTokens());
foreach ($lexer->getTokens() as $token) {
    print($token->toString() . "\n");
}
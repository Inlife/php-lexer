<?php

require 'vendor/autoload.php';

use phpLexer\Lexer;


$lexer = new Lexer(array(
    '$a=123;if($a==123&&$b==false){12'
));


print_r($lexer->getTokens());
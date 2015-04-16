<?php

$message = 'hello';

// No "use"
$example = function () {
    var_dump($message);
};
echo $example();

// Inherit $message
$example = function () use ($message) {
    var_dump($message);
};
echo $example();

// Inherited variable's value is from when the function
// is defined, not when called
$message = 'world';
echo $example();

// Reset message
$message = 'hello';

// Inherit by-reference
$example = function () use (&$message) {
    var_dump($message);
};
echo $example();

// The changed value in the parent scope
// is reflected inside the function call
$message = 'world';
echo $example();

// Closures can also accept regular arguments
$example = function ($arg) use ($message) {
    var_dump($arg . ' ' . $message);
};
$example("hello");
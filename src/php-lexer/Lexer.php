<?php

namespace phpLexer;

use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;

class Lexer// extends AbstractLexer
{
    const REJECT              = 0;
    const INTEGER             = 1;
    const REAL                = 2;
    const IDENTIFIER          = 3;
    const VARIABLE            = 4;
    const OPERATOR            = 5;
    const UNKNOWN             = 10;

    private static $transitions = array(

    );


    // All tokens that are not valid identifiers must be < 100
    // const T_NONE                = 1;
    // const T_INTEGER             = 2;
    // const T_STRING              = 3;
    // const T_INPUT_PARAMETER     = 4;
    // const T_FLOAT               = 5;
    // const T_CLOSE_PARENTHESIS   = 6;
    // const T_OPEN_PARENTHESIS    = 7;
    // const T_COMMA               = 8;
    // const T_DIVIDE              = 9;
    // const T_DOT                 = 10;
    // const T_EQUALS              = 11;
    // const T_GREATER_THAN        = 12;
    // const T_LOWER_THAN          = 13;
    // const T_MINUS               = 14;
    // const T_MULTIPLY            = 15;
    // const T_NEGATE              = 16;
    // const T_PLUS                = 17;
    // const T_OPEN_CURLY_BRACE    = 18;
    // const T_CLOSE_CURLY_BRACE   = 19;
    // const T_ASSIGN              = 20;
    // const T_SEMICOLON           = 21;
    // const T_LOWER_THAN_OR_EQ    = 22;
    // const T_GREATER_THAN_OR_EQ  = 23;
    // const T_INCREMENT           = 24;
    // const T_DECREMENT           = 25;
    // const T_ASSIGN_INCREMENT    = 26;
    // const T_ASSIGN_DECREMENT    = 27;
    // const T_SCOPE_RESOLUTION    = 28;

    // const T_OR                  = 50;
    // const T_AND                 = 51;

    // // All tokens that are also identifiers should be >= 100
    // const T_IDENTIFIER          = 100;
    // const T_VARIABLE            = 101;
    // const T_FUNCTION            = 102;
    // const T_RETURN              = 103;
    // const T_IF                  = 104;
    // const T_ELSE                = 105;
    // const T_FOR                 = 106;
    // const T_WHILE               = 107;
    // const T_BREAK               = 108;
    // const T_CONTINUE            = 109;
    // const T_CASE                = 110;
    // const T_NULL                = 111;
    // const T_FOREACH             = 112;
    // const T_CLASS               = 113;
    // const T_ABSTRACT            = 114;
    // const T_INTERFACE           = 115;
    // const T_EXTENDS             = 116;
    // const T_PUBLIC              = 117;
    // const T_PROTECTED           = 118;
    // const T_PRIVATE             = 119;
    // const T_IMPLEMENTS          = 120;
    // const T_TRAIT               = 121;

    // const T_COMMENT             = 300;

    // const T_OPEN_TAG            = 501;
    // const T_CLOSE_TAG           = 502;

    /**
     * Creates a new query scanner object.
     *
     * @param string $input a query string
     */
    public function __construct($input)
    {
        $this->setInput($input);
    }

    /**
     * @inheritdoc
     */
    // protected function getCatchablePatterns()
    // {
    //     return array(
    //         '\<\?(?:php|)',
    //         '[a-z_\\\][a-z0-9_\\\\]*[a-z0-9_]{1}',  // identifer
    //         '(?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?', // number
    //         "'(?:[^']|'')*'",  // string ''
    //         '"(?:[^"]|"")*"',  // string ""
    //         '[^a-z0-9\s()"\'\[\]{}\$\/\/]{1,2}', // any double-operator
    //         '[\$]{1,2}[_a-z]{1}[a-z0-9_]{0,}',  // variable
    //         '\/\/.{0,}', // line comment
    //         '\/\*(?:[^*]|[\r\n]|(?:\*+(?:[^*\/]|[\r\n])))*\*+\/' // multiblock comment
    //     );
    // }

    /**
     * @inheritdoc
     */
    // protected function getNonCatchablePatterns()
    // {
    //     return array('\s+', '(.)');
    // }

    /**
     * @inheritdoc
     */
    protected function getType(&$value)
    {
        $type = self::T_NONE;

        // Recognizing numeric values
        if (is_numeric($value)) {
            return (strpos($value, '.') !== false || stripos($value, 'e') !== false) 
                    ? self::T_FLOAT : self::T_INTEGER;
        }

        // Differentiate between quoted names, identifiers, input parameters and symbols
        if ($value[0] === "'" || $value[0] === '"') {
            $value = str_replace("''", "'", substr($value, 1, strlen($value) - 2));
            return self::T_STRING;
        } else if (ctype_alpha($value[0]) || $value[0] === '_') {
            $name = 'phpLexer\Lexer::T_' . strtoupper($value);

            if (defined($name)) {
                $type = constant($name);

                if ($type > 100) {
                    return $type;
                }
            }

            return self::T_IDENTIFIER;
        } else if ($value[0] === '$') {
            return self::T_VARIABLE;
        } else if ($value[0] === '<' && $value[1] === '?') {
            return self::T_OPEN_TAG;
        } else if ($value[0] === '/' && ($value[1] === '/' || $value[1] === '*')) {
            return self::T_COMMENT;
        } else {
            switch ($value) {
                case '.': return self::T_DOT;
                case ',': return self::T_COMMA;
                case '(': return self::T_OPEN_PARENTHESIS;
                case ')': return self::T_CLOSE_PARENTHESIS;
                case '+': return self::T_PLUS;
                case '-': return self::T_MINUS;
                case '*': return self::T_MULTIPLY;
                case '/': return self::T_DIVIDE;
                case '!': return self::T_NEGATE;
                case '{': return self::T_OPEN_CURLY_BRACE;
                case '}': return self::T_CLOSE_CURLY_BRACE;
                case '=': return self::T_ASSIGN;
                case ';': return self::T_SEMICOLON;
                case '>': return self::T_GREATER_THAN;
                case '<': return self::T_LOWER_THAN;
                case '==': return self::T_EQUALS;
                case '>=': return self::T_GREATER_THAN_OR_EQ;
                case '<=': return self::T_LOWER_THAN_OR_EQ;
                case '++': return self::T_INCREMENT;
                case '--': return self::T_DECREMENT;
                case '+=': return self::T_ASSIGN_INCREMENT;
                case '-=': return self::T_ASSIGN_DECREMENT;
                case '||': return self::T_OR;
                case '&&': return self::T_AND;
                case '::': return self::T_SCOPE_RESOLUTION;
                default:
                    // Do nothing
                    break;
            }
        }

        return $type;
    }
}





// 123.123 + abs(123, 123);

// $adasd = 12313;
<?php

/**
 * Test file for DisallowTypeLongNamesSniff - Type Casting.
 *
 * Tests detection of long type names in type casting.
 */

// Global type casting with long names.
$globalBool = (boolean) true;
$globalInt = (integer) 123;

// Type casting within functions

// Long name type casts within global function.
function testTypeCasting($value)
{
    $bool = (boolean) $value;
    $int = (integer) $value;
}

class TestClass
{
    // Long name type casts within class method.
    public function methodWithTypeCasting($input)
    {
        $castedBool = (boolean) $input;
        $castedInt = (integer) $input;

        if ($input) {
            $localBool = (boolean) false;
            $localInt = (integer) 456;
        }
    }
}
<?php

// @phpcs:disable Generic.Commenting.DocComment.MissingShort

/**
 * Test file for DisallowTypeLongNamesSniff - Type Casting.
 *
 * Tests detection of long type names in type casting.
 */

// Global type casting with long forms
$globalBool = (boolean) true;
$globalInt = (integer) 123;

// Type casting within functions
/**
 * Type cast within global function.
 */
function testTypeCasting($value)
{
    $bool = (boolean) $value;
    $int = (integer) $value;
}

class TestClass
{
    /**
     * @return array<bool, int>
     */
    public function methodWithTypeCasting($input)
    {
        $castedBool = (boolean) $input;
        $castedInt = (integer) $input;

        if ($input) {
            $localBool = (boolean) false;
            $localInt = (integer) 456;
        }

        return [$castedBool, $castedInt];
    }
}
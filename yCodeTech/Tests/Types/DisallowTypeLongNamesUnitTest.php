<?php

namespace yCodeTech\Tests\Types;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffTestCase;

/**
 * Unit test class for DisallowTypeLongNamesSniff
 * 
 * The code to test are split into several .inc files to cover different scenarios.
 * As documented here: https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/4.x/.github/CONTRIBUTING.md#multiple-test-case-files
 */
class DisallowTypeLongNamesUnitTest extends AbstractSniffTestCase
{
    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $testFile The name of the file being tested.
     *
     * @return array<int, int>
     */
    public function getErrorList($testFile = "")
    {

        switch ($testFile) {
            // Docblock Types
            case "DisallowTypeLongNamesUnitTest.1.inc":
                return [
                    // Docblock type errors
                    12 => 1,  // @param boolean
                    13 => 1,  // @param integer
                    15 => 1,  // @return boolean
                    
                    // Class docblock errors
                    25 => 1,  // @property integer
                    26 => 1,  // @property-read integer
                    27 => 1,  // @property-write boolean
                    28 => 1,  // @method boolean
                    
                    // Property and method docblock errors
                    35 => 1,  // @var boolean
                    42 => 1,  // @param boolean
                    44 => 1,  // @return integer
                ];
            // Type Declaration
            case "DisallowTypeLongNamesUnitTest.2.inc":
                return [
                    15 => 1,  // boolean property
                    16 => 1,  // integer property
                    19 => 3,  // boolean param, integer param, boolean return
                    24 => 2,  // ?boolean param, ?integer return
                    29 => 3,  // boolean|integer param, string|boolean return
                ];
            // Type Casting
            case "DisallowTypeLongNamesUnitTest.3.inc":
                return [
                    // Global type casting errors
                    10 => 1,  // (boolean) cast
                    11 => 1,  // (integer) cast
                    
                    // Function type casting errors
                    18 => 1,  // (boolean) cast
                    19 => 1,  // (integer) cast

                    // Method type casting errors
                    27 => 1,  // (boolean) cast
                    28 => 1,  // (integer) cast
                    31 => 1,  // (boolean) cast
                    32 => 1,  // (integer) cast
                ];
            // Generic Types
            case "DisallowTypeLongNamesUnitTest.4.inc":
                return [
                    // Generic type errors
                    12 => 1,  // @param array<boolean>
                    13 => 1,  // @param array<string, integer>
                    14 => 2,  // @param Collection<boolean, integer>
                    16 => 2,  // @return Map<integer, boolean>
                    
                    // Complex generic errors
                    26 => 2,  // @param Generator<integer, boolean>
                    27 => 1,  // @param Promise<boolean>
                    28 => 1,  // @param Traversable<string, integer>
                    30 => 2,  // @return Iterator<integer, boolean>
                ];
            default:
                return [];
        }
    }

    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return [];
    }
}

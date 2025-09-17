<?php

/**
 * Unit test class for FunctionCommentSniff.
 *
 * @author yCodeTech
 * @copyright Copyright (c) 2025, yCodeTech
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace yCodeTech\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffTestCase;

/**
 * Unit test class for the FunctionCommentSniff.
 *
 * @covers \yCodeTech\Sniffs\Commenting\FunctionCommentSniff
 */
class FunctionCommentUnitTest extends AbstractSniffTestCase
{
    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getErrorList()
    {
        return [
            14 => 1,  // Function missing @return tag
            24 => 1,  // Void function with a @return tag
            34 => 1,  // Void constructor magic method with a @return tag
            44 => 1,  // Generator function missing @return tag (should get iterable)
            56 => 1,  // Generator function with yield from missing @return tag
        ];
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
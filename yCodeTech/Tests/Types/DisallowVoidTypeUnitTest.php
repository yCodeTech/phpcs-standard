<?php

/**
 * Unit test class for DisallowVoidTypeSniff.
 *
 * @author yCodeTech
 * @copyright Copyright (c) 2025, yCodeTech
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace yCodeTech\Tests\Types;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffTestCase;

/**
 * Unit test class for the DisallowVoidTypeSniff.
 *
 * @covers \yCodeTech\Sniffs\Types\DisallowVoidTypeSniff
 */
class DisallowVoidTypeUnitTest extends AbstractSniffTestCase
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
            14 => 1,  // explicitVoidMethod(): void
            19 => 1,  // explicitVoidWithParam(): void
            37 => 1,  // explicitVoidFunction(): void
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

<?php

/**
 * Unit test class for DocblockFormatSniff.
 *
 * @author yCodeTech
 * @copyright Copyright (c) 2025, yCodeTech
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace yCodeTech\Tests\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffTestCase;

/**
 * Unit test class for the DocblockFormatSniff.
 *
 * @covers \yCodeTech\Sniffs\Commenting\DocblockFormatSniff
 */
class DocblockFormatUnitTest extends AbstractSniffTestCase
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
            14 => 1,  // @param with multiple extra spaces
            15 => 1,  // @param with 0 spaces between type and variable
            22 => 1,  // @var with multiple extra spaces
            27 => 1,  // @phpstan-param with multiple extra spaces
            28 => 1,  // @psalm-param with multiple extra spaces
            32 => 1,  // @throws with multiple extra spaces
            33 => 1,  // @see with multiple extra spaces
            42 => 1,  // Missing empty line before @return
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

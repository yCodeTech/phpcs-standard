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
            14 => 1,  // @param with extra spaces
            15 => 1,  // @param with zero spaces
            16 => 1,  // @return without empty line before
            21 => 1,  // @var with multiple spaces
            26 => 1,  // @phpstan-param with incorrect spacing
            27 => 1,  // @psalm-param with incorrect spacing
            31 => 1,  // @throws with incorrect spacing
            32 => 1,  // @see with incorrect spacing
            50 => 1,  // @return without empty line before (when @param exists)
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

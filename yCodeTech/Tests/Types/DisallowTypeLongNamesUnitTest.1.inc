<?php

/**
 * Test file for DisallowTypeLongNamesSniff - Docblock Types.
 *
 * Tests detection of long type names in docblock comments.
 */

/**
 * Function with long type names in docblocks.
 *
 * @param boolean $flag Should be bool
 * @param integer $count Should be int
 *
 * @return boolean Should be bool
 */
function testDocblockTypes(bool $flag, int $count): bool
{
    return true;
}

/**
 * Class with long type in docblock.
 *
 * @property integer $property Should be int
 * @property-read integer $foo Should be int
 * @property-write boolean $bar Should be bool
 * @method boolean isBool() Should be bool
 */
class TestClass
{
    /**
     * Class property with long type in docblock.
     *
     * @var boolean Should be bool
     */
    private bool property;

    /**
     * Method with long type in docblock.
     *
     * @param boolean $flag Should be bool
     *
     * @return integer Should be int
     */
    public function methodWithDocblock($flag)
    {
        return 1;
    }
}
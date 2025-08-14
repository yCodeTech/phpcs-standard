<?php

// @phpcs:disable Generic.Commenting.DocComment.MissingShort

/**
 * Test file for DisallowTypeLongNamesSniff - Type Declarations.
 *
 * Tests detection of long type names in PHP type declarations.
 */

class TestClass
{
    // Type declarations with long types
    private boolean $boolProperty;
    protected integer $intProperty;

    // Function with type declaration errors
    /**
     * @return bool
     */
    public function typeDeclarationErrors(boolean $param1, integer $param2): boolean
    {
        return true;
    }

    // Nullable types with long forms
    /**
     * @return int|null
     */
    public function nullableTypes(?boolean $param): ?integer
    {
        return null;
    }

    // Union types with long forms
    /**
     * @return string|bool
     */
    public function unionTypes(boolean|integer $param): string|boolean
    {
        return "test";
    }
}
<?php

// @phpcs:disable yCodeTech.Commenting.FunctionComment.MissingReturn
// We're not concerned with docblocks here, so we can disable missing return violation.

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
    public function typeDeclarationErrors(boolean $param1, integer $param2): boolean
    {
    }

    // Nullable types with long names
    public function nullableTypes(?boolean $param): ?integer
    {
    }

    // Union types with long names
    public function unionTypes(boolean|integer $param): string|boolean
    {
    }
}
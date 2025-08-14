<?php

// @phpcs:disable Generic.Commenting.DocComment.MissingShort

/**
 * Test file for DisallowTypeLongNamesSniff - Generic Types.
 *
 * Tests detection of long type names in generic type syntax.
 */

// Generic types with long forms
/**
 * @param array<boolean> $bool Array Should be array<bool>
 * @param array<string, integer> $map Should be array<string, int>
 * @param Collection<boolean, integer> $collection Should be Collection<bool, int>
 *
 * @return Map<integer, boolean> Should be Map<int, bool>
 */
function testGenericTypes($bool, $map, $collection)
{
    return [];
}

/**
 * More complex generic examples.
 *
 * @param Generator<integer, boolean> $generator Should be Generator<int, bool>
 * @param Promise<boolean> $promise Should be Promise<bool>
 * @param Traversable<string, integer> $traversable Should be Traversable<string, int>
 *
 * @return Iterator<integer, boolean> Should be Iterator<int, bool>
 */
function testComplexGenerics($generator, $promise, $traversable)
{
    return [];
}
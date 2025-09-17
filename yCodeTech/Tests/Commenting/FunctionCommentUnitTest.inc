<?php

/**
 * Test input file for FunctionCommentSniff.
 */

class TestClass
{
    /**
     * Function missing @return tag.
     *
     * @param string $input Input parameter
     */
    public function testMissingReturn($input)
    {
        return strtoupper($input);
    }

    /**
     * Void function with a `@return` tag.
     *
     * @param string $message Message to display
     *
     * @return void
     */
    public function testVoidFunction($message): void
    {
        echo $message;
    }

    /**
     * Constructor method with a `@return` tag.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Generator function missing iterable @return tag
     *
     * @param array $items Items to iterate
     */
    public function testGeneratorMissingReturn($items)
    {
        foreach ($items as $item) {
            yield $item;
        }
    }

    /**
     * Generator function with `yield from` missing @return tag.
     *
     * @param array $items Items to iterate
     */
    public function testGeneratorYieldFromMissingReturn($items)
    {
        yield from $items;
    }
}
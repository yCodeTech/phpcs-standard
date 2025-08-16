<?php

// @phpcs:disable Generic.Commenting.DocComment.MissingShort

/**
 * Test input file for DocblockFormatSniff.
 */

class TestClass
{
    /**
     * Function with spacing violations.
     *
     * @param    string   $name     Name parameter
     * @param string$value Value parameter
     *
     * @return string
     */
    public function testFunction($name, $value)
    {
        /**
         * @var    string   $variable
         */
        $variable = 'test';

        /**
         * @phpstan-param    string   $param
         * @psalm-param  string   $param
         */

        /**
         * @throws    Exception   If error occurs
         * @see   SomeClass  For reference
         */
        return $name . $value;
    }

    /**
     * Function with missing empty line before `@return`.
     *
     * @param string $test
     * @return string
     */
    public function functionWithParamAndReturn($test)
    {
        return $test;
    }
}
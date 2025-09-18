<?php

/**
 * Test file for yCodeTech PHPCS Standard.
 *
 * This file contains examples of code that should be flagged by our custom sniffs.
 */


/**
 * File docblock with incorrect formatting (should be flagged as error).
 *
 * @author      yCodeTech       <ycodetech@email.com>
 * @copyright        Copyright (c) 2025, yCodeTech
 * @since     1.0.0     Initial version
 */


/***************
 * CLASS TESTS *
 ***************/


/**
 * Testing class-specific tags with incorrect formatting (should be flagged as error).
 *
 * The following should be fixed:
 * - All @ tags must have exactly 1 space between each tag elements (type, variable, etc.).
 * - `boolean` and `integer` types should be corrected to `bool` and `int`.
 *
 * @property       string    $property      regular read/write property
 *
 * @property-read  integer    $foo          the foo prop
 * @property-write boolean    $bar          the bar prop
 *
 * @staticvar  string    $staticVar    Description
 *
 * @method  mixed   get($name)   Description
 */

abstract class TestClass
{
    /************************
     * CLASS PROPERTY TESTS *
     ************************/


    /**
     * Class property with incorrect spacing (should be flagged as error).
     *
     * @var     string             Hello World
     *
     * @psalm-var     string     Hello World
     * @phpstan-var      string     Hello World
     */
    private $property = 'Hello World';

    /**
     * Class property with incorrect integer type name (should be flagged as error).
     *
     * The following should be fixed:
     * - The property type should be changed to `int` in both docblock and type declarations.
     *
     * @var integer
     */
    private integer $foo;

    /**
     * Class property with incorrect boolean type name in an union typing (should be flagged as error).
     *
     * The following should be fixed:
     * - The property type should be changed to `bool` in both docblock and type declarations.
     *
     * @var boolean|string
     */
    private boolean|string $bar;

    private function testFunctionWithoutDocblock(): string {
        // Function without a docblock, a required `@return` tag should NOT be added to
        // the previous docblock ($bar) (should NOT be flagged).
        return 'Hello World';
    }

    private function anotherFunctionWithoutDocblock(): int {
        // Function without a docblock, a required `@return` tag should NOT be added to
        // the previous docblock ($bar) (should NOT be flagged).
        return 42;
    }



    /***************
     * TYPES TESTS *
     ***************/


    /**
     * Function with boolean type (should be flagged as error).
     *
     * The following should be fixed:
     * - The `boolean` type should be changed to `bool` in the docblock, type declarations,
     *   and type casting.
     *
     * @param boolean $flag A boolean flag
     *
     * @return boolean
     */
    public function testBooleanType(boolean $flag): boolean
    {
        return (boolean) $flag;
    }

    /**
     * Function with integer type (should be flagged as error).
     *
     * The following should be fixed:
     * - The `integer` type should be changed to `int` in the docblock, type declarations,
     *   and type casting.
     *
     * @param integer $number A number.
     *
     * @return integer
     */
    public function testIntegerType(integer $number): integer
    {
        return (integer) $number;
    }

    /**
     * Function with nullable boolean and integer types (should be flagged as error).
     *
     * The following should be fixed:
     * - The `integer` and `boolean` types should be changed to `int` and `bool` respectively
     * in the docblock, and nullable type declarations.
     *
     * @param boolean|null $flag A flag.
     * @param integer $number A number.
     *
     * @return integer|null
     */
    public function testNullableType(?boolean $flag, integer $number): ?integer
    {
        return $flag ? $number : null;
    }

    /**
     * Function with generic types with boolean and integer (should be flagged as error).
     *
     * @see https://dev.to/jszutkowski/how-to-start-using-generic-types-in-php-2f1k
     *
     * The following should be fixed:
     * - The `boolean` and `integer` types within the generic types should be changed to
     *   `bool` and `int` respectively in the docblock.
     * - `@param` must have exactly 1 space between each tag elements (type, variable, etc.).
     *
     * @param     array<boolean>          $array
     * @param     Collection<integer>     $numbers
     * @param     Map<string, boolean>    $settings
     * @param     array<string, integer>  $counts
     * @param     array<boolean, integer> $things
     *
     * @return array<boolean>
     */
    public function testGenericTypes(array $array): array
    {
        return $array;
    }


    /*****************
     * SPACING TESTS *
     *****************/


    /**
     * Function with incorrect spacing in `@param` (should be flagged as error).
     *
     * The following should be fixed:
     * - All param tags must have exactly 1 space between each tag elements (type, variable, etc.).
     * - The `@param` that has 0 space between the type and variable should have 1 space.
     *
     * @param    int   $number     Description
     * @param    string$variable     Description
     *
     * @phpstan-param     int     $number     Description
     * @psalm-param    int     $number     Description
     */
    public function testParamSpacing($variable, $number)
    {
        echo $variable . $number;
    }

    /**
     * Function with incorrect spacing in `@return` (should be flagged as error).
     *
     * The following should be fixed:
     * - All return tags must have exactly 1 space between each tag elements (type, variable, etc.).
     * - A new empty line before the normal `@return`.
     *
     * @psalm-return     string
     * @phpstan-return      string
     * @return          string
     */
    public function testReturnSpacing()
    {
        return "Hello World";
    }

    // Function with only a @return tag in the docblock (should NOT be flagged).
    // The following should NOT be fixed:
    // - A new empty line should not be added before the `@return` tag.
    /**
     * @return string
     */
    public function testReturnEmptyLine()
    {
        return "Hello World";
    }

    /**
     * Function with an @ tag that has no content, with another function's docblock below it.
     *
     * The `testTagWithNoContentContinued` function's docblock below should
     * not be considered as part of the `@param` in the docblock below
     * (should NOT be flagged for incorrect `TagSpacing`).
     *
     * @param
     */
    public function testTagWithNoContent() {
    }

    /**
	 * Test function continued from above (should NOT be flagged for incorrect `TagSpacing`).
	 */
    public function testTagWithNoContentContinued() {
    }


    /*************************
     * MISSING @RETURN TESTS *
     *************************/


    /**
     * Function missing `@return` tag (should be flagged as error).
     *
     * The following should be fixed:
     * - A `@return` tag with type of `mixed` should be added and with an empty line before.
     *
     * @param string $input Input parameter
     */
    public function testMissingReturn($input)
    {
        return strtoupper($input);
    }

    /**
     * A generator function (should be flagged as error).
     *
     * The following should be fixed:
     * - A `@return` tag with type of `iterable` should be added.
     */
    public function testMissingGeneratorReturn()
    {
        yield 1;
    }

    /***********************
     * VOID FUNCTION TESTS *
     ***********************/

    /**
     * Function that returns void with an explicit `void` typing
     * (should NOT be flagged for missing `@return` tag).
     *
     * The following should NOT be fixed:
     * - A `@return` tag should not be added for an explicit `void` return.
     *
     * @param string $message Message to display
     */
    public function testExplicitVoidFunction($message): void
    {
        echo $message;
    }

    /**
     * Function that returns void implicitly, ie. no return statement in the body
     * (should NOT be flagged for missing `@return` tag).
     *
     * The following should NOT be fixed:
     * - A `@return` tag should not be added for an implicit `void` return.
     *
     * @param string $message Message to display
     */
    public function testImplicitVoidFunction($message)
    {
        echo $message;
    }

    /**
     * Function with empty return statement
     * (should NOT be flagged for missing `@return` tag).
     *
     * The following should NOT be fixed:
     * - A `@return` tag should not be added for an empty `return`.
     *
     * @param string $condition Some condition
     */
    public function testEmptyReturnFunction($condition)
    {
        if ($condition === 'exit') {
            return;
        }
        echo "Continuing...";
    }

    /**
     * Function that returns void implicitly and has a nested closure that returns a value
     * (should NOT be flagged for missing `@return` tag).
     *
     * The following should NOT be fixed:
     * - A `@return` tag should not be added for a `void` function that has a
     *   nested returnable closure.
     */
    function testVoidFunctionWithNestedClosure()
    {
        $array = [1, 2, 3];

        // This closure returns a value, but it's not in the immediate function scope
        $result = array_map(function($item) {
            return $item * 2; // This return should be ignored
        }, $array);

        echo "Result: " . implode(', ', $result);
    }

    /**
     * Function that returns void implicitly and has a nested anonymous function that returns a
     * value (should NOT be flagged for missing `@return` tag).
     *
     * The following should NOT be fixed:
     * - A `@return` tag should not be added for a `void` function that has a
     *   nested returnable anonymous function.
     */
    function testVoidFunctionWithAnonymousFunction()
    {
        $callback = function($value) {
            return strtoupper($value); // This return should be ignored
        };

        $callback('test');
    }

    /*************************************
     * VOID FUNCTIONS WITH @RETURN TESTS *
     *************************************/

    /**
     * Function that returns void that has a `@return` tag (should be flagged).
     *
     * The following should be fixed:
     * - The `@return` tag should be removed.
     *
     * @return void
     */
    public function testVoidFunctionWithDocblockReturn(): void
    {
        echo "Hello World";
    }

    /**
     * Function that returns void implicitly that has a `@return` tag (should be flagged).
     *
     * The following should be fixed:
     * - The `@return` tag should be removed.
     *
     * @param string $message Message to display
     *
     * @return void
     */
    public function testImplicitVoidFunctionWithDocblockReturn($message)
    {
        echo $message;
    }

    /**
     * Abstract function that returns void with a `@return void` tag (should be flagged).
     *
     * The following should be fixed:
     * - The `@return` tag should be removed.
     *
     * @return void
     */
    abstract public function testAbstractVoidFunction();

    /*****************************************
     * VOID MAGIC METHODS WITH @RETURN TESTS *
     *****************************************/

    /**
     * Construct magic method with unnecessary `@return void` tag (should be flagged).
     *
     * The following should be fixed:
     * - The `@return` tag should be removed.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Destruct magic method with unnecessary `@return void` tag (should be flagged).
     *
     * The following should be fixed:
     * - The `@return` tag should be removed.
     *
     * @return void
     */
    public function __destruct() {
    }

    /**
     * Set magic method with unnecessary `@return void` tag (should be flagged).
     *
     * The following should be fixed:
     * - The `@return` tag should be removed.
     *
     * @return void
     */
    public function __set($name, $value) {
    }

    /************************************
     * ALL OTHER @ TAGS FORMATTING TEST *
     ************************************/


    /**
     * Function with correct formatting (should be flagged as error).
     *
     * The following should be fixed:
     * - All @ tags must have exactly 1 space between each tag elements (type, description, etc.).
     *
     * @throws    Exception     If something goes wrong
     * @see       SomeClass     For more information
     * @uses     SomeTrait    For more information
     * @deprecated       This function is deprecated, use newFunction() instead
     */
    public function testOtherTagsFormatting()
    {
        throw new Exception("This is a test exception");
    }
}


/*****************
 * FUNCTION TEST *
 *****************/


/**
 * Normal function with incorrect formatting (should be flagged as error).
 *
 * The following should be fixed:
 * - All @ tags must have exactly 1 space between each tag elements (type, variable, etc.).
 * - `boolean` and `integer` types should be corrected to `bool` and `int` in both
 *   docblock and type declarations.
 * - A new empty line before `@return`
 *
 * @param    string|integer   $param  Description
 * @return     boolean
 */
function testFunction(string|integer $param): boolean
{
    return $param === 'test' ? true : false;
}

/*********************
 * GLOBAL SCOPE TEST *
 *********************/

/**
 * Global scope type casting (should be flagged as error).
 *
 * The following should be fixed:
 * - The `boolean` and `integer` type casting should be changed to `bool` and `int` respectively.
 */
$bool = (boolean) $value;
$int = (integer) $value;

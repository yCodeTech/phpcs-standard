# yCodeTech PHPCS Standard

[![Latest Stable Version](https://img.shields.io/github/v/release/yCodeTech/phpcs-standard?label=Stable)](https://github.com/PHPCSStandards/PHP_CodeSniffer/releases)
![Minimum PHP Version](https://img.shields.io/packagist/dependency-v/yCodeTech/phpcs-standard/php?label=php)
[![Unit Tests](https://github.com/yCodeTech/phpcs-standard/actions/workflows/unit-tests.yml/badge.svg)](https://github.com/yCodeTech/phpcs-standard/actions/workflows/unit-tests.yml)

A custom PHP_CodeSniffer standard that enforces strict type and docblock rules with comprehensive auto-fixing capabilities.

## Requirements

-   `php >= 7.2`

## Installation

```bash
# Per project:
$ composer require ycodetech/phpcs-standard

# Globally
$ composer global require ycodetech/phpcs-standard
```

## Sniffs

### yCodeTech.Commenting.DocblockFormat

Enforces proper spacing and formatting in docblocks.

<table>
  <tr>
  <th>Rules</th>
  <th>Fixable?</th>
  </tr>
  <tr>
    <td>All docblock tags must have exactly <code>1 space</code> between each element.
    </td>
    <td>✔️</td>
  </tr>
  <tr>
    <td>The type and variable in any tag must be separated by a <code>space</code>.
    </td>
    <td>✔️</td>
  </tr>
  <tr>
    <td><code>@return</code> tags must be preceded by exactly <code>1 empty line</code>.
    </td>
    <td>✔️</td>
  </tr>
</table>

#### Violation Codes:

`yCodeTech.Commenting.DocblockFormat.TagSpacing`
`yCodeTech.Commenting.DocblockFormat.ReturnSpacing`

#### Examples:

<table>
<tr>
  <th>✔️ Valid: Exactly 1 space between tag elements</th>
  <th>❌ Invalid: Multiple spaces between tag elements</th>
</tr>
<tr>
<td>

```php
/**
 * @param string $name The name parameter
 * @throws Exception If something goes wrong
 * @see SomeClass For more information
 * @var string
 * @copyright Copyright (c) year, Name
 */
```

</td>
<td>

```php
/**
 * @param string    $name       The name parameter
 * @throws    Exception    If something goes wrong
 * @see    SomeClass  For more information
 * @var    string
 * @copyright        Copyright (c) year, Name
 */
```

</td>
</tr>
</table>

<table>
<tr>
  <th>✔️ Valid: Exactly 1 space between type and variable</th>
  <th>❌ Invalid: 0 spaces between type and variable</th>
</tr>
<tr>
<td>

```php
/**
 * @param string $name The name parameter
 */
```

</td>
<td>

```php
/**
 * @param string$name The name parameter
 */
```

</td>
</tr>
</table>

<table>
<tr>
  <th>✔️ Valid: Exactly 1 empty line before @return tag</th>
  <th>❌ Invalid: 0 empty lines before @return tag</th>
</tr>
<tr>
<td>

```php
/**
 * @param string $name The name parameter
 *
 * @return string
 */
```

</td>
<td>

```php
/**
 * @param string $name The name parameter
 * @return string
 */
```

</td>
</tr>
</table>

<table>
<tr>
  <th>✔️ Valid: Exactly 1 empty line before @return tag</th>
  <th>❌ Invalid: Multiple empty lines before @return tag</th>
</tr>
<tr>
<td>

```php
/**
 * @param string $name The name parameter
 *
 * @return string
 */
```

</td>
<td>

```php
/**
 * @param string $name The name parameter
 *
 *
 *
 * @return string
 */
```

</td>
</tr>
</table>

### yCodeTech.Commenting.FunctionComment

Functions that return a value must have a `@return` docblock tag.

<table>
  <tr>
    <th>Rules</th>
    <th>Fixable?</th>
    <th>Notes</th>
  </tr>

  <tr>
    <td>Functions with <code>non-void</code> return types (<code>string</code>, <code>bool</code>, etc.) must have a <code>@return</code> tag.
    </td>
    <td>✔️</td>
     <td>

-   Fixes with a <code>mixed</code> return type

-   Magic methods (e.g. <code>**construct</code>, <code>**get</code>, etc.) are exempt.

  </td>
  </tr>
  <tr>
    <td>Functions with <code>void</code> return types must NOT have <code>@return</code> tags, except generator functions.
    </td>
    <td>✔️</td>
    <td></td>
  </tr>
  <tr>
    <td>Generator functions must have a <code>@return</code> tag.
    </td>
    <td>✔️</td>
    <td>Fixes with a <code>iterable</code> return type</td>
  </tr>
</table>

#### Violation Codes:

`yCodeTech.Commenting.FunctionComment.MissingReturn`
`yCodeTech.Commenting.FunctionComment.VoidReturnTagFound`

#### Examples:

<table>
<tr>
  <th>✔️ Valid: @return tag for non-void function</th>
  <th>❌ Invalid: Missing @return tag for non-void function</th>
</tr>
<tr>
<td>

```php
/**
 * Get formatted string.
 *
 * @param string $input The input string
 *
 * @return string
 */
public function formatString(string $input): string
{
}
```

</td>
<td>

```php
/**
 * Get formatted string.
 *
 * @param string $input The input string
 */
public function formatString(string $input): string
{
}
```

</td>
</tr>
</table>

<table>
<tr>
  <th>✔️ Valid: No @return for void function</th>
  <th>❌ Invalid: @return tag on void function</th>
</tr>
<tr>
<td>

```php
/**
 * Process data without returning anything.
 *
 * @param array $data The data to process
 */
public function processData(array $data): void
{
}
```

</td>
<td>

```php
/**
 * Process data without returning anything.
 *
 * @param array $data The data to process
 *
 * @return void
 */
public function processData(array $data): void
{
}
```

</td>
</tr>
</table>

<table>
<tr>
  <th>✔️ Valid: @return tag for generator function</th>
  <th>❌ Invalid: Missing @return tag for generator function</th>
</tr>
<tr>
<td>

```php
/**
 * Get formatted string.
 *
 * @param string $input The input string
 *
 * @return iterable
 */
public function formatString(string $input)
{
    yield "Hello $input";
}
```

</td>
<td>

```php
/**
 * Get formatted string.
 *
 * @param string $input The input string
 */
public function formatString(string $input)
{
    yield "Hello $input";
}
```

</td>
</tr>
</table>

### yCodeTech.Types.DisallowTypeLongNames

Long type names are disallowed. Short names must be used in all contexts.

<table>
  <tr>
    <th>Rules</th>
    <th>Fixable?</th>
  </tr>
  <tr>
    <td>Use <code>bool</code> instead of <code>boolean</code>.</td>
    <td>✔️</td>
  </tr>
  <tr>
    <td>Use <code>int</code> instead of <code>integer</code>.</td>
    <td>✔️</td>
  </tr>
  <tr>
    <th>Contexts</th>
    <td>Docblocks, type declarations, union and nullable types, type casting, generic types</td>
  </tr>
</table>

#### Violation Codes:

`yCodeTech.Types.DisallowTypeLongNames.DocblockType`
`yCodeTech.Types.DisallowTypeLongNames.TypeCast`
`yCodeTech.Types.DisallowTypeLongNames.TypeDeclaration`

#### Examples:

<table>
<tr>
  <th>✔️ Valid: Short name docblock types</th>
  <th>❌ Invalid: Long name docblock types</th>
</tr>
<tr>
<td>

```php
/**
 * @param bool $isValid
 * @psalm-param bool $isValid
 *
 * @return int
 */
```

</td>
<td>

```php
/**
 * @param boolean $isValid
 * @psalm-param boolean $isValid
 *
 * @return integer
 */
```

</td>
</tr>
</table>
<table>
<tr>
  <th>✔️ Valid: Short name docblock generic types</th>
  <th>❌ Invalid: Long name docblock generic types</th>
</tr>
<tr>
<td>

```php
/**
 * @param Collection<int> $numbers
 * @param Map<string, bool> $settings
 * @param array<string, int> $counts
 * @param array<bool, int> $counts
 */
```

</td>
<td>

```php
/**
 * @param Collection<integer> $numbers
 * @param Map<string, boolean> $settings
 * @param array<string, integer> $counts
 * @param array<boolean, integer> $counts
 */
```

</td>
</tr>
</table>
<table>
<tr>
  <th>✔️ Valid: Short name class property type declarations</th>
  <th>❌ Invalid: Long name class property type declarations</th>
</tr>
<tr>
<td>

```php
private bool $isActive;
protected int $userCount;
```

</td>
<td>

```php
private boolean $isActive;
protected integer $userCount;
```

</td>
</tr>
</table>
<table>
<tr>
  <th>✔️ Valid: Short name function/method/closure types</th>
  <th>❌ Invalid: Long name function/method/closure types</th>
</tr>
<tr>
<td>

```php
function foo(bool $flag): int {
    $callback = function(bool $isValid): int {
    };
}
```

</td>
<td>

```php
function foo(boolean $flag): integer {
    $callback = function(boolean $isValid): integer {
    };
}
```

</td>
</tr>
</table>

<table>
<tr>
  <th>✔️ Valid: Short name nullable and union types</th>
  <th>❌ Invalid: Long name nullable and union types</th>
</tr>
<tr>
<td>

```php
function foo(?bool $flag, bool|string $var): ?int {
}
```

</td>
<td>

```php
function foo(?boolean $flag, boolean|string $var): ?integer {
}
```

</td>
</tr>
</table>

<table>
<tr>
  <th>✔️ Valid: Short name type casting</th>
  <th>❌ Invalid: Long name type casting</th>
</tr>
<tr>
<td>

```php
$foo = (bool) $isValid;
$bar = (int) $count;
```

</td>
<td>

```php
$foo = (boolean) $isValid;
$bar = (integer) $count;
```

</td>
</tr>
</table>

## Testing

To test the standard against the provided comprehensive test file, please see [the specific instructions](./test_utils/README.md).

### Unit Tests

Run the comprehensive test suite:

```bash
# Test all sniff unit tests
$ composer test
```

Each test file contains deliberate violations that should be detected by the corresponding sniff.

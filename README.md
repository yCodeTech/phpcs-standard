# yCodeTech PHPCS Standard

A custom PHP_CodeSniffer standard that enforces strict type and docblock rules with comprehensive auto-fixing capabilities.

## Requirements Enforced

This standard enforces the following rules:

1. **Type Short Names**: Use `bool` instead of `boolean` and `int` instead of `integer` in ALL contexts: docblock tags, type declarations (returns, params, props), and type casting.
2. **Return Spacing**: Require 1 single empty line before `@return` tag
3. **Parameter Spacing**: Require 1 single space before and after `@param` types
4. **Return Tag**: Require a `@return` tag except when the return type is `void`

## Installation

1. Clone or download this repository
2. Install dependencies:
   ```bash
   composer install
   ```

## Usage

### Basic Usage

Run PHPCS with the custom standard:

```bash
vendor/bin/phpcs --standard=yCodeTech /path/to/your/php/files
```

### Auto-fixing

Run PHPCBF to automatically fix violations where possible:

```bash
vendor/bin/phpcbf --standard=yCodeTech /path/to/your/php/files
```

### Testing

Run the comprehensive test suite:

```bash
# Test all sniffs against test files
composer test-sniffs

# Test against example files
composer test

# Test all together
composer test-all
```

The test suite includes:
- **DisallowTypeLongNamesSniff** - Tests `boolean`/`integer` → `bool`/`int` conversions in all contexts
- **DocblockFormatSniff** - Tests spacing and formatting rules
- **FunctionCommentSniff** - Tests missing `@return` tag detection

Each test file contains deliberate violations that should be detected by the corresponding sniff.

See `yCodeTech/Tests/README.md` for detailed testing documentation.

## 📁 File Structure

```
yCodeTech-PHPCS-Standard/
├── phpcs.xml                               # Dev-only ruleset to include the main standard's ruleset as phpcs-only for vscode intellisense for testing.
├── composer.json                           # Composer configuration
├── yCodeTech/                              # The yCodeTech Standard
│   ├── ruleset.xml                         # yCodeTech standard ruleset
│   ├── Sniffs/
│   │   ├── Types/
│   │   │   └── DisallowTypeLongNamesSniff.php # Disallow Type Long Names sniff
│   │   └── Commenting/
│   │       ├── DocblockFormatSniff.php     # Enforces spacing rules
│   │       └── FunctionCommentSniff.php    # Enforces @return tag requirement
│   ├── Tests/
│   │   ├── Types/
│   │   │   └── # Unit Test files
│   │   └── Commenting/
│   │       └── # Unit Test files
│   └── Docs/
│        ├── Types/
│        │   └── # Documentation
│        └── Commenting/
│            └── # Documentation
└── tests/
    └── TestFile.php                        # Integration test file
```

## Custom Sniffs

### DisallowTypeLongNamesSniff
- **Purpose**: Enforces the use of short name types (`bool`, `int`) instead of long names (`boolean`, `integer`)
- **Contexts**: Docblocks, type declarations, union types, nullable types, type casting
- **Fixable**: Yes
- **Examples**:
  ```php
  // Bad
  @param boolean $flag
  function test(integer $num): boolean { return (boolean) $num; }
  
  // Good
  @param bool $flag
  function test(int $num): bool { return (bool) $num; }
  ```

### DocblockFormatSniff
- **Purpose**: Enforces proper spacing in docblocks
- **Rules**:
  - 1 empty line before `@return` tag
  - 1 space between `@param` type and variable
- **Fixable**: Yes
- **Example**:
  ```php
  // Bad
  /**
   * @param string$variable
   * @return string
   */
  
  // Good
  /**
   * @param string $variable
   *
   * @return string
   */
  ```

### FunctionCommentSniff
- **Purpose**: Enforces `@return` tag requirement
- **Rules**:
  - Functions must have `@return` tag unless they return `void`
  - Functions with `void` return type should not have `@return` tag (warning)
- **Fixable**: Yes (with a `mixed` type, which allows manual adjustment)
- **Example**:
  ```php
  // Bad - missing @return
  /**
   * @param string $input
   */
  public function process($input) {
      return strtoupper($input);
  }
  
  // Good
  /**
   * @param string $input
   *
   * @return string
   */
  public function process($input): string {
      return strtoupper($input);
  }
  ```

## Testing

Test the standard against the provided test file:

```bash
vendor/bin/phpcs --standard=./ruleset.xml ./tests/TestFile.php
```

Expected violations in the test file:
- Long name type usage in docblocks, type declarations, and type casting (fixable)
- Incorrect parameter spacing (fixable)
- Missing @return tags (fixable)
- Missing empty lines before @return tags (fixable)

## Configuration

The `ruleset.xml` file can be customized to:
- Include/exclude specific sniffs
- Adjust error severity levels
- Add file patterns to ignore
- Extend additional standards

## Extending

To add more custom sniffs:

1. Create new sniff files in `yCodeTech/Sniffs/[Category]/`
2. Follow the naming convention: `[SniffName]Sniff.php`
3. Implement the `PHP_CodeSniffer\Sniffs\Sniff` interface
4. Add the sniff reference to `ruleset.xml`

## Requirements

- PHP 7.4 or higher
- PHP_CodeSniffer 3.7 or higher

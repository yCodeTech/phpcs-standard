# yCodeTech PHPCS Standard - Unit Tests

This directory contains comprehensive unit tests for the yCodeTech PHPCS Standard.

## Test Structure

```
yCodeTech/Tests/
├── Types/
│   ├── DisallowTypeLongNamesUnitTest.php   # Unit test for DisallowTypeLongNamesSniff
│   └── DisallowTypeLongNamesUnitTest.inc   # Test input file with violations
├── Commenting/
│   ├── DocblockFormatSniffTest.php         # Unit test for DocblockFormatSniff
│   ├── DocblockFormatSniffTest.inc         # Test input file with violations
│   ├── FunctionCommentSniffTest.php        # Unit test for FunctionCommentSniff
│   └── FunctionCommentSniffTest.inc        # Test input file with violations
├── StandardIntegrationTest.php             # Integration test for entire standard
└── StandardIntegrationTest.inc             # Comprehensive test file
```

## Running Tests

### Option 1: Using Composer Scripts (Recommended)

```bash
# Test all sniffs against their test files
composer test-sniffs

# Test against main test file
composer test

# Run all tests together
composer test-all
```

### Option 2: Manual Testing

Test individual sniffs:
```bash
# Test DisallowTypeLongNamesSniff
vendor/bin/phpcs --standard=./yCodeTech/ruleset.xml yCodeTech/Tests/Types/DisallowTypeLongNamesUnitTest.inc

# Test DocblockFormatSniff
vendor/bin/phpcs --standard=./yCodeTech/ruleset.xml yCodeTech/Tests/Commenting/DocblockFormatUnitTest.inc

# Test FunctionCommentSniff
vendor/bin/phpcs --standard=./yCodeTech/ruleset.xml yCodeTech/Tests/Commenting/FunctionCommentUnitTest.inc
```

### Option 3: Auto-fix Testing

Test that violations can be automatically fixed:
```bash
# Copy test file and run auto-fix
cp yCodeTech/Tests/Types/DisallowTypeLongNamesUnitTest.inc temp_test.php
vendor/bin/phpcbf --standard=./yCodeTech/ruleset.xml temp_test.php
rm temp_test.php
```

## Test Coverage

### DisallowTypeLongNamesSniff Tests
- ✅ `boolean` → `bool` conversion in @param tags
- ✅ `integer` → `int` conversion in @param tags  
- ✅ `boolean` → `bool` conversion in @return tags
- ✅ `integer` → `int` conversion in @return tags
- ✅ `boolean` → `bool` conversion in @var tags
- ✅ `integer` → `int` conversion in @var tags
- ✅ `boolean` → `bool` conversion in @property tags
- ✅ `integer` → `int` conversion in @property tags
- ✅ Static analysis tags (@phpstan-*, @psalm-*)
- ✅ Union types (boolean|string → bool|string)
- ✅ Function parameter type declarations
- ✅ Function return type declarations
- ✅ Class property type declarations
- ✅ Type casting (boolean) → (bool), (integer) → (int)
- ✅ Nullable types (?boolean → ?bool)
- ✅ Context-aware detection (no duplicates)

### DocblockFormatSniff Tests
- ✅ Exactly 1 space between @param elements
- ✅ Zero space detection and fixing
- ✅ Multiple space detection and fixing  
- ✅ Empty line before @return tag
- ✅ All @ tag types (param, return, var, throws, see, etc.)
- ✅ Static analysis tag spacing

### FunctionCommentSniff Tests
- ✅ Missing @return tag detection
- ✅ Auto-insertion of @return mixed
- ✅ Void function detection (explicit void)
- ✅ Implicit void function detection (echo-only)
- ✅ Empty return statement detection
- ✅ Correct indentation for auto-inserted tags

## Expected Results

Each test file contains deliberate violations that should be detected by the corresponding sniff. The test classes define the exact line numbers where errors should occur.

When running the tests, you should see:
- **All violations detected** on the expected lines
- **Auto-fixing capabilities** working correctly
- **No false positives** on correct code

## Integration Testing

The `StandardIntegrationTest` provides comprehensive testing of all sniffs working together, ensuring:
- No conflicts between sniffs
- Proper violation detection across all rule types
- Consistent behavior of the entire standard

## Adding New Tests

To add tests for new sniffs:

1. Create `YourSniffTest.php` in the appropriate directory
2. Create `YourSniffTest.inc` with test violations
3. Extend `AbstractSniffUnitTest`
4. Define expected error lines in `getErrorList()`
5. Add test to the test runner scripts

## Test Results Validation

✅ **All sniffs properly detect violations**  
✅ **Auto-fixing works without formatting issues**  
✅ **No false positives on correct code**  
✅ **Comprehensive coverage of all rule types**  
✅ **Integration testing passes**

The unit test suite ensures the yCodeTech PHPCS Standard is robust, reliable, and ready for production use!

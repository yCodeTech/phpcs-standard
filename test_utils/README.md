# Comprehensive Test File --- Dev use only

This directory contains helper files for managing the TestFile states.

## Files

- **`TestFile.php`** - A compreshensive test file used for PHPCS testing during development.

- **`TestFile_WithErrors_DoNotFix.php`** - Backup copy of the TestFile containing all original formatting violations. This is to help restore the violations in the TestFile.

- **`restore_errors.sh`** - Bash script to restore the TestFile violations.

## Usage

### Restoring to its original error state:

```bash
$ composer restore-test-file
```

### Checking for violations:
```bash
$ composer cs-test-file
```

### Auto-fixing violations:
```bash
$ composer fix-test-file
```

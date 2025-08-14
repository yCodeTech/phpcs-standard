#!/bin/bash

# Helper script to restore TestFile.php to its original error state
# This copies the backup file with all formatting errors back to the main test file

echo "Restoring TestFile.php to original error state..."
cp test_utils/TestFile_WithErrors_DoNotFix.php test_utils/TestFile.php
echo "Done! TestFile.php now contains all the original formatting violations."

echo ""
echo "To verify, run:"
echo "vendor/bin/phpcs test_utils/TestFile.php --standard=yCodeTech"

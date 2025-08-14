<?php

/**
 * FunctionComment sniff for yCodeTech PHPCS Standard.
 *
 * Enforces @return tag requirement except when return is void.
 *
 * @category PHP
 * @package PHP_CodeSniffer
 * @author yCodeTech
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace yCodeTech\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * FunctionComment sniff.
 *
 * Enforces @return tag requirement except when return is void.
 */
class FunctionCommentSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int>
     */
    public function register()
    {
        return [T_FUNCTION, T_CLOSURE];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the
     *                                                   stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find the function name
        $namePtr = $phpcsFile->findNext(T_STRING, $stackPtr);
        if ($namePtr === false) {
            return;
        }

        // Skip magic methods and constructors/destructors
        $functionName = $tokens[$namePtr]['content'];
        if (substr($functionName, 0, 2) === '__') {
            return;
        }

        // Find the docblock for this function
        $commentEnd = $phpcsFile->findPrevious(T_DOC_COMMENT_CLOSE_TAG, ($stackPtr - 1));
        if ($commentEnd === false) {
            // No docblock found, skip functions without docblocks
            return;
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];
        // Check if function returns void
        $hasVoidReturn = $this->hasVoidReturn($phpcsFile, $stackPtr);
        // Look for @return tag in the docblock
        $hasReturnTag = false;
        for ($i = $commentStart; $i <= $commentEnd; $i++) {
            if ($tokens[$i]['code'] === T_DOC_COMMENT_TAG && $tokens[$i]['content'] === '@return') {
                $hasReturnTag = true;
                break;
            }
        }

        // Check if the function is a generator.
        // If so, then it should have @return tag with type iterable.
        $isGeneratorFunction = $this->isGeneratorFunction($phpcsFile, $stackPtr);

        // If function doesn't return void, it must have @return tag
        if ((!$hasVoidReturn || $isGeneratorFunction) && !$hasReturnTag) {
            $error = 'Missing @return tag in function comment';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'MissingReturn');
            if ($fix === true) {
                // Add @return tag to the docblock
                $this->addReturnTag($phpcsFile, $commentEnd, $isGeneratorFunction ? 'iterable' : 'mixed');
            }
        }
    }

    /**
     * Check if function has void return type or returns nothing.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the function token.
     *
     * @return bool
     */
    private function hasVoidReturn(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find the opening parenthesis of the function parameters
        $openParen = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        if ($openParen === false) {
            return false;
        }

        // Find the closing parenthesis of the function parameters
        $closeParen = $tokens[$openParen]['parenthesis_closer'] ?? null;
        if ($closeParen === null) {
            return false;
        }

        // Check for explicit void return type declaration between ) and {
        $colonPtr = $phpcsFile->findNext(T_COLON, $closeParen + 1, $tokens[$stackPtr]['scope_opener'] ?? null);

        if ($colonPtr !== false) {
            $returnTypePtr = $phpcsFile->findNext(T_STRING, $colonPtr);
            if ($returnTypePtr !== false) {
                $returnType = $tokens[$returnTypePtr]['content'];
                if ($returnType === 'void') {
                    return true;
                }
            }
        }

        // Check if function implicitly returns void by analysing the function body
        return $this->hasImplicitVoidReturn($phpcsFile, $stackPtr);
    }

    /**
     * Check if function implicitly returns void (no return statements or only empty returns).
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the function token.
     *
     * @return bool
     */
    private function hasImplicitVoidReturn(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Get the function's scope
        $scopeOpener = $tokens[$stackPtr]['scope_opener'] ?? null;
        $scopeCloser = $tokens[$stackPtr]['scope_closer'] ?? null;

        if ($scopeOpener === null || $scopeCloser === null) {
            return false; // Abstract function or interface method
        }

        // Look for return statements within the function scope
        $returnPtr = $scopeOpener;
        while (($returnPtr = $phpcsFile->findNext(T_RETURN, $returnPtr + 1, $scopeCloser)) !== false) {
            // Check what follows the return statement (skip whitespace)
            $nextToken = $phpcsFile->findNext(T_WHITESPACE, $returnPtr + 1, null, true);

            // If the next non-whitespace token is a semicolon, it's "return;" (empty return)
            if ($nextToken !== false && $tokens[$nextToken]['code'] !== T_SEMICOLON) {
                // There's something after return that's not a semicolon, so it returns a value
                return false;
            }
        }

        // If we get here, either:
        // 1. No return statements found (functions that just echo/print), or
        // 2. Only "return;" statements found (without values)
        // Both cases mean the function implicitly returns void
        return true;
    }

    /**
     * Check if function is a generator function (contains yield statements).
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the function token.
     *
     * @return bool
     */
    private function isGeneratorFunction(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Get the function's scope
        $scopeOpener = $tokens[$stackPtr]['scope_opener'] ?? null;
        $scopeCloser = $tokens[$stackPtr]['scope_closer'] ?? null;

        if ($scopeOpener === null || $scopeCloser === null) {
            return false; // Abstract function or interface method
        }

        // Look for yield statements within the function scope
        $yieldPtr = $phpcsFile->findNext([T_YIELD, T_YIELD_FROM], $scopeOpener + 1, $scopeCloser);
        if ($yieldPtr !== false) {
            return true;
        }

        // If we get here, then no yield statements were found,
        // so it's not a generator function.
        return false;
    }

    /**
     * Add @return tag to a docblock.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $commentEnd The position of the docblock closing tag.
     * @param string $returnType The return type to use.
     *
     * @return void
     */
    private function addReturnTag(File $phpcsFile, $commentEnd, $returnType)
    {
        $tokens = $phpcsFile->getTokens();
        $phpcsFile->fixer->beginChangeset();

        // Find the last line of content in the docblock (before the closing tag)
        $lastContentLine = $commentEnd - 1;
        while (
            $lastContentLine > 0 &&
            ($tokens[$lastContentLine]['code'] === T_DOC_COMMENT_WHITESPACE ||
                trim($tokens[$lastContentLine]['content']) === '')
        ) {
            $lastContentLine--;
        }

        // Find the indentation by looking at existing docblock content
        $commentStart = $tokens[$commentEnd]['comment_opener'];
        $baseIndent = '';

        // Look for the first content line to get proper indentation
        for ($i = $commentStart + 1; $i < $commentEnd; $i++) {
            if (
                $tokens[$i]['code'] === T_DOC_COMMENT_STRING ||
                $tokens[$i]['code'] === T_DOC_COMMENT_TAG
            ) {
                // Found a content line, get the base indentation by looking at the line start
                $lineStart = $i;
                while (
                    $lineStart > $commentStart &&
                    $tokens[$lineStart]['line'] === $tokens[$i]['line']
                ) {
                    $lineStart--;
                }
                $lineStart++; // Move to first token on this line

                // Get everything before the * character
                $beforeStar = '';
                while ($lineStart < $commentEnd && $tokens[$lineStart]['content'] !== '*') {
                    $beforeStar .= $tokens[$lineStart]['content'];
                    $lineStart++;
                }

                if ($lineStart < $commentEnd) {
                    $baseIndent = $beforeStar;
                }
                break;
            }
        }

        // Fallback to standard indentation if no content found
        if ($baseIndent === '') {
            $baseIndent = '     '; // 5 spaces standard for docblocks
        }

        // Add empty line and @return tag with the return type.
        $newContent = $phpcsFile->eolChar . $baseIndent . '*' . $phpcsFile->eolChar
            . $baseIndent . '* @return ' . $returnType;

        $phpcsFile->fixer->addContent($lastContentLine, $newContent);
        $phpcsFile->fixer->endChangeset();
    }
}

<?php

/**
 * DisallowVoidType sniff for yCodeTech PHPCS Standard.
 *
 * Disallows explicit void return type declarations on functions and methods.
 *
 * @category PHP
 * @package PHP_CodeSniffer
 * @author yCodeTech
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace yCodeTech\Sniffs\Types;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * DisallowVoidType sniff.
 *
 * Disallows explicit `: void` return type declarations on functions, methods,
 * and closures. The absence of a return type already implies void.
 */
class DisallowVoidTypeSniff implements Sniff
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
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $voidPtr = $this->getExplicitVoidTypePtr($phpcsFile, $stackPtr);
        if ($voidPtr === false) {
            return;
        }

        $error = 'Explicit void return type is not allowed and should be removed';
        $fix = $phpcsFile->addFixableError($error, $voidPtr, 'Found');
        if ($fix === true) {
            $this->removeExplicitVoidType($phpcsFile, $stackPtr);
        }
    }

    /**
     * Get the position of the explicit `void` return type token, if present.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the function token.
     *
     * @return int|false The position of the void token, or false if not found.
     */
    private function getExplicitVoidTypePtr(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $openParen = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        if ($openParen === false) {
            return false;
        }

        $closeParen = $tokens[$openParen]['parenthesis_closer'] ?? null;
        if ($closeParen === null) {
            return false;
        }

        $scopeOpener = $tokens[$stackPtr]['scope_opener'] ?? null;
        $semicolonPtr = $phpcsFile->findNext(T_SEMICOLON, $closeParen + 1);
        $searchEnd = $scopeOpener ?? ($semicolonPtr !== false ? $semicolonPtr + 1 : null);

        $colonPtr = $phpcsFile->findNext(T_COLON, $closeParen + 1, $searchEnd);
        if ($colonPtr === false) {
            return false;
        }

        $returnTypePtr = $phpcsFile->findNext(T_WHITESPACE, $colonPtr + 1, null, true);
        if ($returnTypePtr === false) {
            return false;
        }

        if ($tokens[$returnTypePtr]['code'] === T_STRING && $tokens[$returnTypePtr]['content'] === 'void') {
            return $returnTypePtr;
        }

        return false;
    }

    /**
     * Remove explicit `: void` return type from a function signature.
     *
     * Removes the colon, any whitespace between colon and void, and the `void`
     * token itself, leaving the rest of the signature intact.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the function token.
     */
    private function removeExplicitVoidType(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $openParen = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        if ($openParen === false) {
            return;
        }

        $closeParen = $tokens[$openParen]['parenthesis_closer'] ?? null;
        if ($closeParen === null) {
            return;
        }

        $scopeOpener = $tokens[$stackPtr]['scope_opener'] ?? null;
        $semicolonPtr = $phpcsFile->findNext(T_SEMICOLON, $closeParen + 1);
        $searchEnd = $scopeOpener ?? ($semicolonPtr !== false ? $semicolonPtr + 1 : null);

        $colonPtr = $phpcsFile->findNext(T_COLON, $closeParen + 1, $searchEnd);
        if ($colonPtr === false) {
            return;
        }

        $voidPtr = $phpcsFile->findNext(T_WHITESPACE, $colonPtr + 1, null, true);
        if ($voidPtr === false || $tokens[$voidPtr]['content'] !== 'void') {
            return;
        }

        $phpcsFile->fixer->beginChangeset();
        for ($i = $colonPtr; $i <= $voidPtr; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }
        $phpcsFile->fixer->endChangeset();
    }
}

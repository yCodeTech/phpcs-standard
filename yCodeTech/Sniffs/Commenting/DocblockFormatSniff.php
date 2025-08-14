<?php

/**
 * DocblockFormat sniff for yCodeTech PHPCS Standard.
 *
 * Enforces specific formatting rules for docblocks including:
 * - 1 single empty line before @return tag
 * - 1 single space before and after @param types
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
 * DocblockFormat sniff.
 *
 * Enforces proper spacing and formatting in docblocks.
 */
class DocblockFormatSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int>
     */
    public function register()
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];

        // Handle @return specific rules (empty line before @return)
        if ($content === '@return') {
            $this->checkReturnEmptyLine($phpcsFile, $stackPtr);
        }

        // Apply general spacing rules to ALL @ tags (including @return and @param)
        $this->checkTagSpacing($phpcsFile, $stackPtr);
    }

    /**
     * Check empty line before @return tag.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the @return token.
     *
     * @return void
     */
    private function checkReturnEmptyLine(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        
        // First, check if there are any other @ tags before this @return
        $hasOtherTags = false;
        $docBlockStart = $phpcsFile->findPrevious([T_DOC_COMMENT_OPEN_TAG], $stackPtr);
        if ($docBlockStart !== false) {
            for ($i = $docBlockStart + 1; $i < $stackPtr; $i++) {
                if ($tokens[$i]['code'] === T_DOC_COMMENT_TAG) {
                    $hasOtherTags = true;
                    break;
                }
            }
        }
        
        // If there are no other tags before @return, don't require empty line
        if (!$hasOtherTags) {
            return;
        }
        
        // Find the previous non-whitespace token
        $prev = $phpcsFile->findPrevious([T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR], ($stackPtr - 1), null, true);
        if ($prev === false) {
            return;
        }

        // Count empty lines between previous content and @return
        $emptyLines = 0;
        $currentPtr = $prev + 1;

        while ($currentPtr < $stackPtr) {
            if (
                $tokens[$currentPtr]['code'] === T_DOC_COMMENT_WHITESPACE &&
                strpos($tokens[$currentPtr]['content'], "\n") !== false
            ) {
                $emptyLines++;
            }
            $currentPtr++;
        }

        // We want exactly 1 empty line before @return.
        // We use 2 here because we count the line with content + 1 empty line.
        if ($emptyLines !== 2) {
            $error = 'There must be exactly 1 empty line before @return tag';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ReturnSpacing');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

                // Remove existing whitespace
                for ($i = $prev + 1; $i < $stackPtr; $i++) {
                    if ($tokens[$i]['line'] === T_DOC_COMMENT_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                }

                // Add empty line.
                $indent = str_repeat(' ', $tokens[$stackPtr]['column']);
                $phpcsFile->fixer->addContent($prev, $phpcsFile->eolChar . $indent . '*' . $phpcsFile->eolChar);
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Check spacing for all @ tags (unified method).
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the @ tag token.
     *
     * @return void
     */
    private function checkTagSpacing(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check spacing between @ tag and its content
        $next = $phpcsFile->findNext([T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STRING], ($stackPtr + 1));
        if ($next === false) {
            return;
        }

        $tagName = $tokens[$stackPtr]['content'];
        $needsFixing = false;
        $contentToken = $next;

        // If we found whitespace, check if it's exactly one space
        if ($tokens[$next]['code'] === T_DOC_COMMENT_WHITESPACE) {
            $whitespaceContent = $tokens[$next]['content'];

            // If it's not exactly one space, we need to fix it
            if ($whitespaceContent !== ' ') {
                $needsFixing = true;
            }
            
            // Move to next token which should be the string
            $contentToken = $phpcsFile->findNext(T_DOC_COMMENT_STRING, ($next + 1));
            if ($contentToken === false) {
                return;
            }
        }

        $content = $tokens[$contentToken]['content'];

        // Ensure there's a space between type and variable
        // This regex matches types that directly touch variables (no space)
        if (preg_match('/^[^\s]+\$/', $content)) {
            // Found a pattern like "string$variable" or "array<int,bool>$variable" - missing space
            $needsFixing = true;
        }

        // Replace all spaces that are 2 or more consecutive spaces, with a single space
        $normalizedContent = preg_replace('/( ){2,}/', ' ', $content);
        
        // Fix missing space between type and variable
        $normalizedContent = preg_replace('/^([^\s]+)(\$\w+)/', '$1 $2', $normalizedContent);
        
        // Check if content needs normalization
        if ($content !== $normalizedContent) {
            $needsFixing = true;
        }

        if ($needsFixing) {
            $error = "There must be exactly 1 space between elements in $tagName";
            $fix = $phpcsFile->addFixableError($error, $contentToken, 'TagSpacing');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                
                // If there was a whitespace token, replace it and normalize content
                if ($tokens[$next]['code'] === T_DOC_COMMENT_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken($next, ' ');
                    $phpcsFile->fixer->replaceToken($contentToken, $normalizedContent);
                }
                
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}

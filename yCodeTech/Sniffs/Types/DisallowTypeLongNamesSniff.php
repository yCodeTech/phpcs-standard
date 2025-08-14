<?php

/**
 * DisallowTypeLongNames sniff for yCodeTech PHPCS Standard.
 *
 * Disallows long type names in both docblocks and PHP type declarations.
 * Replaces 'boolean' with 'bool' and 'integer' with 'int' everywhere.
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
 * DisallowTypeLongNames sniff.
 *
 * Disallows long type names in both docblocks, type declarations, and type casting.
 * Handles `@param`, `@return`, `@var` and other type-like tags as well as function/method
 * parameters, returns, and class property type declarations.
 */
class DisallowTypeLongNamesSniff implements Sniff
{
    /**
     * Type name mappings long => short.
     * Short names are the real names of scalar types. The longer names are only aliases.
     *
     * @see https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.base.scalar
     *
     * @var array<string, string>
     */
    private $typeNames = [
        "boolean" => "bool",
        "integer" => "int"
    ];

    /**
     * Track processed tokens to avoid duplicates.
     *
     * @var array<string, array<int, bool>>
     */
    private $processedTokens = [];

    /**
     * Returns an array of tokens this test wants to listen for.
     * 
     * Once PHP_CodeSniffer encounters one of these tokens, it calls the process method.
     *
     * @return array<int>
     */
    public function register()
    {
        return [
            T_DOC_COMMENT_TAG,  // For docblock type checking
            T_FUNCTION,         // For function/method type declarations
            T_CLOSURE,          // For closure type declarations
            T_VARIABLE,         // For class property type declarations
            T_BOOL_CAST,        // For (boolean) and (bool) casting
            T_INT_CAST,         // For (integer) and (int) casting
        ];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Reset processed tokens for each file
        $filename = $phpcsFile->getFilename();
        if (!isset($this->processedTokens[$filename])) {
            $this->processedTokens[$filename] = [];
        }

        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        // Docblock tags
        if ($token['code'] === T_DOC_COMMENT_TAG) {
            $this->processDocblockTag($phpcsFile, $stackPtr);
        }
        // Function type declarations
        elseif ($token['code'] === T_FUNCTION || $token['code'] === T_CLOSURE) {
            $this->processFunctionTypes($phpcsFile, $stackPtr);
        }
        // Class property type declarations
        elseif ($token['code'] === T_VARIABLE) {
            $this->processPropertyType($phpcsFile, $stackPtr);
        }
        // Type casting
        elseif ($token['code'] === T_BOOL_CAST || $token['code'] === T_INT_CAST) {
            $this->checkTypeToken($phpcsFile, $stackPtr, 'typecast');
        }
    }

    /**
     * Process docblock tags for long type names.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack.
     *
     * @return void
     */
    private function processDocblockTag(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $tagName = $tokens[$stackPtr]['content'];

        // Only process typeable tags,
        $typeableTags = [
            "param",
            "return",
            "var",
            "property",
            "method",
        ];

        // Remove the @ symbol from the tag name for comparison.
        $tagNameWithoutAt = ltrim($tagName, '@');

        $matched = false;
        foreach ($typeableTags as $typeableTag) {
            // Check if the tag name contains any of the typeable tag names.
            // This allows matching @phpstan-param, @psalm-param, etc. to 'param'.
            // If the tag matches, then we will process it.
            if (strpos($tagNameWithoutAt, $typeableTag) !== false) {
                $matched = true;
                break;
            }
        }
        // If the tag name is NOT matched, skip processing the tag.
        if (!$matched) {
            return;
        }

        // Find the next string token which should contain the type
        $stringPtr = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $stackPtr + 1, null, false, null, true);
        if ($stringPtr === false) {
            return;
        }

        $this->checkTypeToken($phpcsFile, $stringPtr, 'docblock', $tagName);
    }

    /**
     * Process function/method parameter and return types.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack.
     *
     * @return void
     */
    private function processFunctionTypes(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find function opening parenthesis.
        $openParen = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        if ($openParen === false) {
            return;
        }

        $closeParen = $tokens[$openParen]['parenthesis_closer'];

        /**
         * Parameter Types
         */

        // Check parameter types, these could be singular types or union types.
        for ($i = $openParen + 1; $i < $closeParen; $i++) {
            if ($tokens[$i]['code'] === T_STRING) {
                $this->checkTypeToken($phpcsFile, $i, 'parameter');
            }
        }

        $colon = $phpcsFile->findNext(T_COLON, $closeParen);

        // Find the opening brace or semicolon
        // (needed for both return type checking and type cast checking)
        // If colon is found, we use it to find the opening brace/semicolon,
        // otherwise we use the closing parenthesis.
        $searchStart = $colon !== false ? $colon : $closeParen;
        $openBrace = $phpcsFile->findNext([T_OPEN_CURLY_BRACKET, T_SEMICOLON], $searchStart);

        /**
         * Return Types
         */

        // Check return type.
        if ($colon !== false) {
            // Find all T_STRING tokens in the return type declaration
            // These could be singular types or union types like: boolean, string|integer, etc.
            for ($i = $colon + 1; $i < $openBrace; $i++) {
                if ($tokens[$i]['code'] === T_STRING) {
                    $this->checkTypeToken($phpcsFile, $i, 'return');
                }
            }
        }
    }

    /**
     * Process class property type declarations.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack.
     *
     * @return void
     */
    private function processPropertyType(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Look backwards to find the visibility keyword.
        $visibilityTokens = [T_PUBLIC, T_PRIVATE, T_PROTECTED, T_STATIC, T_VAR];
        $visibilityPtr = $phpcsFile->findPrevious($visibilityTokens, $stackPtr - 1);
        
        if ($visibilityPtr === false) {
            return;
        }

        // Check if there's a function keyword or an open parenthesis between visibility
        // and variable. If there is, then it's a function parameter, not a class property,
        // so just return.
        $functionPtr = $phpcsFile->findNext([T_FUNCTION, T_OPEN_PARENTHESIS], $visibilityPtr, $stackPtr);
        if ($functionPtr !== false) {
            return;
        }

        // Find all T_STRING tokens between visibility and the variable
        // These should be class property type declarations (singular or union types)
        for ($i = $visibilityPtr + 1; $i < $stackPtr; $i++) {
            if ($tokens[$i]['code'] === T_STRING) {
                $this->checkTypeToken($phpcsFile, $i, 'property');
            }
        }
    }

    /**
     * Check a specific type token.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the type token.
     * @param string $context The context (parameter, return, property).
     * @param array $data Data to format the error message with.
     *
     * @return void
     */
    private function checkTypeToken(File $phpcsFile, $stackPtr, $context, $docblockTagName = null)
    {
        // Check if this token position has already been processed
        $filename = $phpcsFile->getFilename();
        if (isset($this->processedTokens[$filename][$stackPtr])) {
            return;
        }
        
        // Mark this token as processed
        $this->processedTokens[$filename][$stackPtr] = true;

        $tokens = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]['content'];
        $replacedContent = $content;

        // Extract the type from the original content
        $extractedTypes = $this->extractTypesFromContent($content);

        // Process each long-form type found
        foreach ($extractedTypes as $extractedType) {
            // Get the short type name from the mapping.
            $shortType = $this->typeNames[$extractedType];
            
            // This pattern matches for both simple types and types within generic type syntax,
            // using word boundaries to avoid partial matches.
            $pattern = '/\b' . preg_quote($extractedType, '/') . '\b/i';

            // Replace long name type with short name in the entire content.
            $replacedContent = preg_replace($pattern, $shortType, $replacedContent);
            
            // Double-check that replacement actually happened, by comparing the original
            // content with the replaced content.
            // If replacement occurred, we will report an error.
            if ($replacedContent !== $content) {
                $this->reportAndFixError(
                    $phpcsFile,
                    $stackPtr,
                    $extractedType,
                    $shortType,
                    $context,
                    $docblockTagName,
                    $replacedContent
                );
            }
        }
    }

    /**
     * Report and fix a type error.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the type token.
     * @param string $extractedType The long-form type found.
     * @param string $shortType The short-form replacement.
     * @param string $context The context (parameter, return, property, etc.).
     * @param string|null $docblockTagName The docblock tag name if applicable.
     * @param string $replacedContent The replacement content.
     *
     * @return void
     */
    private function reportAndFixError(
        File $phpcsFile,
        $stackPtr,
        $extractedType,
        $shortType,
        $context,
        $docblockTagName,
        $replacedContent
    ) {
        // Docblocks
        if ($context === 'docblock') {
            $error = 'Short type names must be used in docblocks: "%s" must be "%s" in "%s" tags.';
            $code = 'DocblockType';
            $contextForError = $docblockTagName;
        }
        // Type Casts
        elseif ($context === 'typecast') {
            $error = 'Short type names must be used in type casting: "%s" must be "%s".';
            $code = 'TypeCast';
            $contextForError = null;
        }
        // Type Declarations (function/method/class property)
        else {
            $error = 'Short type names must be used in type declarations: "%s" must be "%s" for %s type.';
            $code = 'TypeDeclaration';
            $contextForError = $context;
        }

        $errorData = $contextForError
            ? [$extractedType, $shortType, $contextForError]
            : [$extractedType, $shortType];

        $fix = $phpcsFile->addFixableError($error, $stackPtr, $code, $errorData);

        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, $replacedContent);
        }
    }

    /**
     * Extract all long-form type names from the content.
     *
     * @param string $content
     *
     * @return array<string>
     */
    private function extractTypesFromContent($content)
    {
        $foundTypes = [];

        // Type Casting

        // Handle type casting syntax like `(boolean)` or `(integer)`.
        // If we find a long name type, we will extract it and return it.
        if (preg_match('/^\(([^)]+)\)/', $content, $matches)) {
            $castType = $matches[1];
            if (array_key_exists($castType, $this->typeNames)) {
                $foundTypes[] = $castType;
            }
            return $foundTypes;
        }

        $trimmedContent = trim($content);

        // Generic Types

        // Check if we have a generic type in docblocks.
        // We need to extract the type portion carefully to handle generic types with
        // spaces like `Map<string, boolean>`.
        if (preg_match('/^([^<]+<[^>]+>)/', $trimmedContent, $matches)) {
            $typeString = $matches[1];
            // Check for long name types within the generic syntax.

            // Get the long type names from the mapping.
            $longTypeNames = array_keys($this->typeNames);

            // Create a regex pattern to match any of the long type names
            // This pattern uses word boundaries to ensure we match whole words only.
            $longTypePattern = '\b(' . implode('|', array_map('preg_quote', $longTypeNames)) . ')\b';

            // Find ALL long name types in the generic type
            if (preg_match_all('/' . $longTypePattern . '/i', $typeString, $typeMatches)) {
                $foundTypes = array_merge($foundTypes, $typeMatches[1]);
            }
        }

        // All other cases: Normal Types and Union Types.

        // For docblock content, we expect the type to be the first word.
        // So we extract by splitting on whitespace, which handles cases like
        // `integer $var Description`.
        // For type declarations it'll just output the string as the first array item anyway.
        $parts = explode(' ', $trimmedContent);
        $first = $parts[0];

        // Split the type as it can be a union type, separated by '|'.
        $typeNames = explode('|', $first);

        foreach ($typeNames as $type) {
            // Clean up the type (remove whitespace)
            $type = trim($type);

            // If the type is in our mapping, then add it to found types.
            if (array_key_exists($type, $this->typeNames)) {
                $foundTypes[] = $type;
            }
        }

        // Return all found long-form types
        return array_unique($foundTypes);
    }
}

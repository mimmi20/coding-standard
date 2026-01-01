<?php

/**
 * This file is part of the coding-standard package.
 *
 * Copyright (c) 2020-2026, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20CodingStandard\Sniffs\Commenting;

use Override;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function array_keys;
use function array_unique;
use function count;
use function explode;
use function is_int;
use function is_string;
use function mb_strlen;
use function mb_strpos;
use function mb_strrpos;
use function mb_substr;
use function mb_trim;

use const T_ANON_CLASS;
use const T_ATTRIBUTE_END;
use const T_CATCH;
use const T_CLOSURE;
use const T_COMMENT;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STRING;
use const T_FUNCTION;
use const T_NAME_FULLY_QUALIFIED;
use const T_NAME_QUALIFIED;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_STRING;
use const T_THROW;
use const T_VARIABLE;
use const T_WHITESPACE;

/**
 * Verifies that a @throws tag exists for each exception type a function throws.
 *
 * @phpcs:disable Generic.Metrics.CyclomaticComplexity.MaxExceeded
 * @phpcs:disable SlevomatCodingStandard.Complexity.Cognitive.ComplexityTooHigh
 * @phpcs:disable SlevomatCodingStandard.Functions.FunctionLength.FunctionLength
 */
final class FunctionCommentThrowTagSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int>
     *
     * @throws void
     */
    #[Override]
    public function register(): array
    {
        return [T_FUNCTION];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile the file being scanned
     * @param int  $stackPtr  the position of the current token
     *                        in the stack passed in $tokens
     *
     * @throws RuntimeException
     */
    #[Override]
    public function process(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $find               = Tokens::METHOD_MODIFIERS;
        $find[T_WHITESPACE] = T_WHITESPACE;

        $commentEnd = null;

        for ($commentEnd = $stackPtr - 1; 0 <= $commentEnd; --$commentEnd) {
            if (isset($find[$tokens[$commentEnd]['code']]) === true) {
                continue;
            }

            if (
                $tokens[$commentEnd]['code'] === T_ATTRIBUTE_END
                && isset($tokens[$commentEnd]['attribute_opener']) === true
            ) {
                $commentEnd = (int) $tokens[$commentEnd]['attribute_opener'];

                continue;
            }

            break;
        }

        if ($commentEnd === null) {
            $phpcsFile->addError(
                error: 'Missing doc comment',
                stackPtr: $stackPtr,
                code: 'MissingFunctionComment',
            );

            return;
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            // Inline comments might just be closing comments for
            // control structures or functions instead of function comments
            // using the wrong comment type. If there is other code on the line,
            // assume they relate to that code.
            $prev = $phpcsFile->findPrevious(types: $find, start: $commentEnd - 1, exclude: true);

            if ($prev !== false && $tokens[$prev]['line'] === $tokens[$commentEnd]['line']) {
                $commentEnd = $prev;
            }
        }

        if (
            $tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            $function = $phpcsFile->getDeclarationName($stackPtr);
            $phpcsFile->addError(
                error: 'Missing doc comment for function %s()',
                stackPtr: $stackPtr,
                code: 'MissingFunctionComment',
                data: [$function],
            );

            return;
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError(
                error: 'You must use "/**" style comments for a function comment',
                stackPtr: $stackPtr,
                code: 'WrongStyle',
            );

            return;
        }

        if ($tokens[$commentEnd]['line'] !== $tokens[$stackPtr]['line'] - 1) {
            for ($i = $commentEnd + 1; $i < $stackPtr; ++$i) {
                if ($tokens[$i]['column'] !== 1) {
                    continue;
                }

                if (
                    $tokens[$i]['code'] === T_WHITESPACE
                    && $tokens[$i]['line'] !== $tokens[$i + 1]['line']
                ) {
                    $error = 'There must be no blank lines after the function comment';
                    $phpcsFile->addError(error: $error, stackPtr: $commentEnd, code: 'SpacingAfter');

                    break;
                }
            }
        }

        // Find all the exception type tokens within the current scope.
        $thrownExceptions = [];
        $currPos          = $stackPtr;
        $unknownCount     = 0;

        if (isset($tokens[$stackPtr]['scope_closer'])) {
            $stackPtrEnd = $tokens[$stackPtr]['scope_closer'];

            do {
                $currPos = $phpcsFile->findNext(
                    types: [T_THROW, T_ANON_CLASS, T_CLOSURE],
                    start: (int) ($currPos + 1),
                    end: (int) ($stackPtrEnd),
                );

                if ($currPos === false) {
                    break;
                }

                if ($tokens[$currPos]['code'] !== T_THROW) {
                    $currPos = $tokens[$currPos]['scope_closer'];

                    continue;
                }

                /*
                    If we can't find a NEW, we are probably throwing
                    a variable or calling a method.

                    If we're throwing a variable, and it's the same variable as the
                    exception container from the nearest 'catch' block, we take that exception
                    as it is likely to be a re-throw.

                    If we can't find a matching catch block, or the variable name
                    is different, it's probably a different variable, so we ignore it,
                    but they still need to provide at least one @throws tag, even through we
                    don't know the exception class.
                 */

                $nextToken = $phpcsFile->findNext(
                    types: T_WHITESPACE,
                    start: (int) ($currPos + 1),
                    exclude: true,
                );

                if (
                    $tokens[$nextToken]['code'] === T_NEW
                    || $tokens[$nextToken]['code'] === T_NS_SEPARATOR
                    || $tokens[$nextToken]['code'] === T_STRING
                ) {
                    /* @phpcs:disable SlevomatCodingStandard.ControlStructures.RequireTernaryOperator.TernaryOperatorNotUsed */
                    if ($tokens[$nextToken]['code'] === T_NEW) {
                        /*
                        var_dump(
                            $tokens[$nextToken],
                            $tokens[$nextToken + 1],
                            $tokens[$nextToken + 2],
                        );
                         */
                        $currException = $phpcsFile->findNext(
                            types: [
                                T_NAME_QUALIFIED,
                                T_NAME_FULLY_QUALIFIED,
                                T_NS_SEPARATOR,
                                T_STRING,
                            ],
                            start: (int) ($currPos),
                            end: (int) ($stackPtrEnd),
                            local: true,
                        );
                    } else {
                        $currException = $nextToken;
                    }

                    /* @phpcs:enable SlevomatCodingStandard.ControlStructures.RequireTernaryOperator.TernaryOperatorNotUsed */

                    if ($currException !== false) {
                        $endException = $phpcsFile->findNext(
                            types: [
                                T_NS_SEPARATOR,
                                T_STRING,
                            ],
                            start: (int) ($currException + 1),
                            end: (int) ($stackPtrEnd),
                            exclude: true,
                            local: true,
                        );

                        $thrownExceptions[] = $endException === false
                            ? $tokens[$currException]['content']
                            : $phpcsFile->getTokensAsString(
                                $currException,
                                $endException - $currException,
                            );
                    }
                } elseif ($tokens[$nextToken]['code'] === T_VARIABLE) {
                    // Find the nearest catch block in this scope and, if the caught var
                    // matches our re-thrown var, use the exception types being caught as
                    // exception types that are being thrown as well.
                    $catch = $phpcsFile->findPrevious(
                        types: T_CATCH,
                        start: (int) ($currPos),
                        end: (int) ($tokens[$stackPtr]['scope_opener']),
                    );

                    if ($catch !== false) {
                        $thrownVar = $phpcsFile->findPrevious(
                            types: T_VARIABLE,
                            start: (int) ($tokens[$catch]['parenthesis_closer'] - 1),
                            end: (int) ($tokens[$catch]['parenthesis_opener']),
                        );

                        if ($tokens[$thrownVar]['content'] === $tokens[$nextToken]['content']) {
                            $exceptions = explode(
                                '|',
                                (string) $phpcsFile->getTokensAsString(
                                    start: (int) ($tokens[$catch]['parenthesis_opener'] + 1),
                                    length: (int) ($thrownVar - $tokens[$catch]['parenthesis_opener'] - 1),
                                ),
                            );

                            foreach ($exceptions as $exception) {
                                $thrownExceptions[] = mb_trim($exception);
                            }
                        }
                    }
                } else {
                    ++$unknownCount;
                }
            } while ($currPos < $stackPtrEnd && $currPos !== false);
        }

        // Only need one @throws tag for each type of exception thrown.
        $thrownExceptions = array_unique($thrownExceptions);

        $throwTags    = [];
        $commentStart = $tokens[$commentEnd]['comment_opener'];

        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if (!is_int($tag) || $tokens[$tag]['content'] !== '@throws') {
                continue;
            }

            if ($tokens[$tag + 2]['code'] !== T_DOC_COMMENT_STRING) {
                continue;
            }

            $exception = $tokens[$tag + 2]['content'];
            $space     = mb_strpos((string) $exception, ' ');

            if ($space !== false) {
                $exception = mb_substr((string) $exception, 0, $space);
            }

            $throwTags[$exception] = true;
        }

        if (empty($throwTags) === true) {
            $error = 'Missing @throws tag in function comment';
            $phpcsFile->addError(error: $error, stackPtr: $commentEnd, code: 'MissingAtThrow');

            return;
        }

        if (empty($thrownExceptions) === true) {
            // If token count is zero, it means that only variables are being
            // thrown, so we need at least one @throws tag (checked above).
            // Nothing more to do.
            return;
        }

        // Make sure @throws tag count matches thrown count.
        $thrownCount = count($thrownExceptions) + $unknownCount;
        $tagCount    = count($throwTags);

        foreach ($thrownExceptions as $throw) {
            if (isset($throwTags[$throw]) === true) {
                continue;
            }

            foreach (array_keys($throwTags) as $tag) {
                if (!is_string($tag)) {
                    continue;
                }

                if (
                    mb_strrpos($tag, (string) $throw) === mb_strlen($tag) - mb_strlen((string) $throw)
                ) {
                    continue 2;
                }
            }

            $error = 'Missing @throws tag for "%s" exception';
            $data  = [$throw];
            $phpcsFile->addError(
                error: $error,
                stackPtr: $commentEnd,
                code: 'MissingThrowForException',
                data: $data,
            );
        }

        if ($tagCount >= $thrownCount) {
            return;
        }

        $error = 'Expected %s @throws tag(s) in function comment; %s found';
        $data  = [
            $thrownCount,
            $tagCount,
        ];
        $phpcsFile->addError(error: $error, stackPtr: $commentEnd, code: 'WrongNumber', data: $data);
    }
}

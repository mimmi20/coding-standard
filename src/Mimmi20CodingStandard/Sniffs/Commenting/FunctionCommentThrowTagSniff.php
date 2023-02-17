<?php
/**
 * This file is part of the coding-standard package.
 *
 * Copyright (c) 2020-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

/**
 * Verifies that a @throws tag exists for each exception type a function throws.
 */

namespace Mimmi20CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

use function array_unique;
use function count;
use function explode;
use function mb_strlen;
use function mb_strpos;
use function mb_strrpos;
use function mb_substr;
use function trim;

use const T_ANON_CLASS;
use const T_CATCH;
use const T_CLOSURE;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STRING;
use const T_FUNCTION;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_STRING;
use const T_THROW;
use const T_VARIABLE;
use const T_WHITESPACE;

final class FunctionCommentThrowTagSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return int[]
     *
     * @throws void
     */
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
     * @throws void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if (false === isset($tokens[$stackPtr]['scope_closer'])) {
            // Abstract or incomplete.
            return;
        }

        $find   = Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, $stackPtr - 1, null, true);
        if (T_DOC_COMMENT_CLOSE_TAG !== $tokens[$commentEnd]['code']) {
            // Function doesn't have a doc comment or is using the wrong type of comment.
            return;
        }

        $stackPtrEnd = $tokens[$stackPtr]['scope_closer'];

        // Find all the exception type tokens within the current scope.
        $thrownExceptions = [];
        $currPos          = $stackPtr;
        $unknownCount     = 0;

        do {
            $currPos = $phpcsFile->findNext([T_THROW, T_ANON_CLASS, T_CLOSURE], $currPos + 1, $stackPtrEnd);
            if (false === $currPos) {
                break;
            }

            if (T_THROW !== $tokens[$currPos]['code']) {
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

            $nextToken = $phpcsFile->findNext(T_WHITESPACE, $currPos + 1, null, true);
            if (
                T_NEW === $tokens[$nextToken]['code']
                || T_NS_SEPARATOR === $tokens[$nextToken]['code']
                || T_STRING === $tokens[$nextToken]['code']
            ) {
                if (T_NEW === $tokens[$nextToken]['code']) {
                    $currException = $phpcsFile->findNext(
                        [
                            T_NS_SEPARATOR,
                            T_STRING,
                        ],
                        $currPos,
                        $stackPtrEnd,
                        false,
                        null,
                        true,
                    );
                } else {
                    $currException = $nextToken;
                }

                if (false !== $currException) {
                    $endException = $phpcsFile->findNext(
                        [
                            T_NS_SEPARATOR,
                            T_STRING,
                        ],
                        $currException + 1,
                        $stackPtrEnd,
                        true,
                        null,
                        true,
                    );

                    if (false === $endException) {
                        $thrownExceptions[] = $tokens[$currException]['content'];
                    } else {
                        $thrownExceptions[] = $phpcsFile->getTokensAsString($currException, $endException - $currException);
                    }
                }
            } elseif (T_VARIABLE === $tokens[$nextToken]['code']) {
                // Find the nearest catch block in this scope and, if the caught var
                // matches our re-thrown var, use the exception types being caught as
                // exception types that are being thrown as well.
                $catch = $phpcsFile->findPrevious(
                    T_CATCH,
                    $currPos,
                    $tokens[$stackPtr]['scope_opener'],
                    false,
                    null,
                    false,
                );

                if (false !== $catch) {
                    $thrownVar = $phpcsFile->findPrevious(
                        T_VARIABLE,
                        $tokens[$catch]['parenthesis_closer'] - 1,
                        $tokens[$catch]['parenthesis_opener'],
                    );

                    if ($tokens[$thrownVar]['content'] === $tokens[$nextToken]['content']) {
                        $exceptions = explode('|', $phpcsFile->getTokensAsString($tokens[$catch]['parenthesis_opener'] + 1, $thrownVar - $tokens[$catch]['parenthesis_opener'] - 1));
                        foreach ($exceptions as $exception) {
                            $thrownExceptions[] = trim($exception);
                        }
                    }
                }
            } else {
                ++$unknownCount;
            }
        } while ($currPos < $stackPtrEnd && false !== $currPos);

        // Only need one @throws tag for each type of exception thrown.
        $thrownExceptions = array_unique($thrownExceptions);

        $throwTags    = [];
        $commentStart = $tokens[$commentEnd]['comment_opener'];

        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ('@throws' !== $tokens[$tag]['content']) {
                continue;
            }

            if (T_DOC_COMMENT_STRING !== $tokens[$tag + 2]['code']) {
                continue;
            }

            $exception = $tokens[$tag + 2]['content'];
            $space     = mb_strpos($exception, ' ');
            if (false !== $space) {
                $exception = mb_substr($exception, 0, $space);
            }

            $throwTags[$exception] = true;
        }

        if (true === empty($throwTags)) {
            $error = 'Missing @throws tag in function comment';
            $phpcsFile->addError($error, $commentEnd, 'Missing');

            return;
        }

        if (true === empty($thrownExceptions)) {
            // If token count is zero, it means that only variables are being
            // thrown, so we need at least one @throws tag (checked above).
            // Nothing more to do.
            return;
        }

        // Make sure @throws tag count matches thrown count.
        $thrownCount = count($thrownExceptions) + $unknownCount;
        $tagCount    = count($throwTags);

        foreach ($thrownExceptions as $throw) {
            if (true === isset($throwTags[$throw])) {
                continue;
            }

            foreach ($throwTags as $tag => $ignore) {
                if (mb_strrpos($tag, $throw) === mb_strlen($tag) - mb_strlen($throw)) {
                    continue 2;
                }
            }

            $error = 'Missing @throws tag for "%s" exception';
            $data  = [$throw];
            $phpcsFile->addError($error, $commentEnd, 'Missing', $data);
        }

        if ($tagCount >= $thrownCount) {
            return;
        }

        $error = 'Expected %s @throws tag(s) in function comment; %s found';
        $data  = [
            $thrownCount,
            $tagCount,
        ];
        $phpcsFile->addError($error, $commentEnd, 'WrongNumber', $data);
    }
}

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

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPhpVersion(PhpVersion::PHP_83)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: false,
        typeDeclarations: true,
        instanceOf: false,
        earlyReturn: true,
    )
    ->withPhpSets(php85: true)
    ->withAttributesSets(phpunit: true)
    ->withComposerBased(phpunit: true)
    ->withSkip([
        NullToStrictStringFuncCallArgRector::class,
    ])
    ->withoutParallel()
    ->withMemoryLimit('2048M');

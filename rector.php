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
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
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
        typeDeclarationDocblocks: true,
        naming: true,
        namedArgs: true,
        instanceOf: false,
        if: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
        phpunitNarrowAsserts: true,
        phpunitMockToStub: true,
    )
    ->withPhpSets(php85: true)
    ->withAttributesSets(phpunit: true)
    ->withComposerBased(phpunit: true)
    ->withSkip([
        PreferPHPUnitThisCallRector::class,
    ])
    ->withoutParallel()
    ->withMemoryLimit('2048M');

<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\YieldDataProviderRector;
use Rector\PHPUnit\CodeQuality\Rector\FuncCall\AssertFuncCallToPHPUnitAssertRector;
use Rector\PHPUnit\CodeQuality\Rector\MethodCall\StringCastAssertStringContainsStringRector;
use Rector\TypeDeclaration\Rector\ClassMethod\NarrowObjectReturnTypeRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../../src',
        __DIR__ . '/../../test',
    ])
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        codeQuality: true,
        typeDeclarations: true,
        phpunitCodeQuality: true,
    )
    ->withRules([
        PreferPHPUnitSelfCallRector::class,
    ])
    ->withConfiguredRule(AddOverrideAttributeToOverriddenMethodsRector::class, ['allow_override_empty_method' => true])
    ->withSkip([
        // self::assertFoo() is preferred
        PreferPHPUnitThisCallRector::class,
        // Don't convert all data providers to Generators - this is just noise
        YieldDataProviderRector::class,
        // Sometimes we use native `assert` inside tests and that's OK
        AssertFuncCallToPHPUnitAssertRector::class,
        // No. Do not narrow types to specific implementations
        NarrowObjectReturnTypeRector::class,
        // SA will tell me if I pass a non-string to assertStringContainsString() and this rector gets it wrong
        StringCastAssertStringContainsStringRector::class,
    ]);

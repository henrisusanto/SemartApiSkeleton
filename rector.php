<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\CodeQuality\Rector\ClassMethod\DateTimeToDateTimeInterfaceRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayReturnDocTypeRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(Setlist::PHP_70);
    $containerConfigurator->import(Setlist::PHP_71);
    $containerConfigurator->import(Setlist::PHP_72);
    $containerConfigurator->import(Setlist::PHP_73);
    $containerConfigurator->import(Setlist::PHP_74);
    $containerConfigurator->import(Setlist::PHP_80);
    $containerConfigurator->import(SetList::EARLY_RETURN);
    $containerConfigurator->import(Setlist::TYPE_DECLARATION);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::PATHS, [__DIR__ . '/lib']);

    $parameters->set(Option::SKIP, [
        // follow convention, eg: Twig extension @see https://symfony.com/doc/current/templating/twig_extension.html
        CallableThisArrayToAnonymousFunctionRector::class,

        AddArrayReturnDocTypeRector::class => [
            // mostly class-string[] is enough for collection of class-string return
            __DIR__ . '/lib/DataFixtures/',
        ],

        // still buggy on property @see https://github.com/rectorphp/rector/issues/6644
        DateTimeToDateTimeInterfaceRector::class,

        ExplicitBoolCompareRector::class => [
            // may have different result on explode() usage
            // @see https://github.com/rectorphp/rector-src/pull/709
            __DIR__ . '/lib/Cron/CronExtension.php',
            __DIR__ . '/lib/Media/Storage.php',
        ],
    ]);
};


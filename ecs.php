<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer;
use PhpCsFixerCustomFixers\Fixer\NoDuplicatedImportsFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\DocBlockLineLengthFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PARALLEL, true);
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
    ]);
    $parameters->set(Option::SKIP, [
        __DIR__ . '/src/DependencyInjection/Configuration.php',
    ]);

    $containerConfigurator->import(SetList::SYMPLIFY);
    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::PHP_CS_FIXER);
    $containerConfigurator->import(SetList::DOCTRINE_ANNOTATIONS);
    $containerConfigurator->import(SetList::CLEAN_CODE);

    $services = $containerConfigurator->services();

    $services->set(DocBlockLineLengthFixer::class)
        ->call('configure', [[
            DocBlockLineLengthFixer::LINE_LENGTH => 120,
        ]]);

    $services->set(YodaStyleFixer::class)
        ->call('configure', [[
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ]]);

    $services->set(PhpdocTypesOrderFixer::class)
        ->call('configure', [[
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ]]);

    $services->set(OrderedImportsFixer::class)
        ->call('configure', [[
            'imports_order' => ['class', 'function', 'const'],
        ]]);

    $services->set(ConcatSpaceFixer::class)
        ->call('configure', [[
            'spacing' => 'one',
        ]]);

    $services->set(LineLengthFixer::class)
        ->call('configure', [[
            LineLengthFixer::LINE_LENGTH => 120,
        ]]);

    $services->set(NoUnusedImportsFixer::class);
    $services->set(NoDuplicatedImportsFixer::class);
};

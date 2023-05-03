<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Williarin\WordpressInterop\Persistence\DuplicationService;
use Williarin\WordpressInterop\Persistence\DuplicationServiceInterface;

return static function(ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(DuplicationService::class)
        ->factory([null, 'create']);

    $services->alias(DuplicationServiceInterface::class, DuplicationService::class)
        ->public();
};

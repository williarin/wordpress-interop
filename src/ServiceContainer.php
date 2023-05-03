<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class ServiceContainer
{
    private ContainerBuilder $container;

    public function __construct()
    {
        $this->initContainer();
    }

    public function get(string $id): ?object
    {
        return $this->container->get($id);
    }

    private function initContainer(): void
    {
        $container = new ContainerBuilder();

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.php');

        $container->compile();

        $this->container = $container;
    }
}

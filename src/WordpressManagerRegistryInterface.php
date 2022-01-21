<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

interface WordpressManagerRegistryInterface
{
    public function getDefaultManagerName(): string;

    public function getManager(string $name = null): WordpressManagerInterface;

    /**
     * @return WordpressManagerInterface[]
     */
    public function getManagers(): array;

    /**
     * @return string[]
     */
    public function getManagerNames(): array;
}

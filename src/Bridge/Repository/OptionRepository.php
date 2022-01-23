<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Option;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Repository\RepositoryInterface;

use function Williarin\WordpressInterop\Util\String\unserialize_if_needed;

final class OptionRepository implements RepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
    }

    public function getEntityClassName(): string
    {
        return Option::class;
    }

    public function findValueByName(string $optionName): string|array|null
    {
        /** @var string|false $result */
        $result = $this->entityManager->getConnection()->createQueryBuilder()
            ->select('option_value')
            ->from($this->entityManager->getTablesPrefix() . 'options')
            ->where('option_name = :name')
            ->setParameter('name', $optionName)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne()
        ;

        return $result === false ? null : unserialize_if_needed($result);
    }

    public function getSiteUrl(): string
    {
        return $this->findValueByName('siteurl');
    }
}

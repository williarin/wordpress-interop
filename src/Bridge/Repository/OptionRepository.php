<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Option;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use Williarin\WordpressInterop\Exception\OptionAlreadyExistsException;
use Williarin\WordpressInterop\Exception\OptionNotFoundException;
use Williarin\WordpressInterop\Repository\RepositoryInterface;
use function Symfony\Component\String\u;
use function Williarin\WordpressInterop\Util\String\unserialize_if_needed;

/**
 * @method string getSiteUrl()
 * @method string getHome()
 * @method string getBlogName()
 * @method string getBlogDescription()
 * @method array  getActivePlugins()
 */
final class OptionRepository implements RepositoryInterface
{
    public const OPTION_SITE_URL = 'siteurl';
    public const OPTION_BLOG_NAME = 'blogname';
    public const OPTION_BLOG_DESCRIPTION = 'blogdescription';

    public function __construct(
        private EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
    }

    public function __call(string $name, array $arguments): string|array|null
    {
        if (!str_starts_with($name, 'get')) {
            throw new MethodNotFoundException(self::class, $name);
        }

        $optionName = u(substr($name, 3))
            ->snake()
            ->upper()
            ->toString()
        ;
        $constantName = sprintf('OPTION_%s', $optionName);

        try {
            $optionName = (new \ReflectionClassConstant($this, $constantName))->getValue();
        } catch (\ReflectionException) {
            $optionName = u(substr($name, 3))
                ->snake()
                ->lower()
                ->toString()
            ;
        }

        return $this->find($optionName, $arguments[0] ?? true);
    }

    public function getEntityClassName(): string
    {
        return Option::class;
    }

    public function find(string $optionName, bool $unserialize = true): string|array|null
    {
        /** @var string|false $result */
        $result = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select('option_value')
            ->from($this->entityManager->getTablesPrefix() . 'options')
            ->where('option_name = :name')
            ->setParameter('name', $optionName)
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne()
        ;

        if ($result === false) {
            throw new OptionNotFoundException($optionName);
        }

        return $unserialize ? unserialize_if_needed($result) : $result;
    }

    public function create(string $optionName, mixed $optionValue): bool
    {
        try {
            $this->find($optionName);

            throw new OptionAlreadyExistsException($optionName);
        } catch (OptionNotFoundException) {
        }

        if (is_array($optionValue)) {
            $optionValue = serialize($optionValue);
        }

        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->insert($this->entityManager->getTablesPrefix() . 'options')
            ->values([
                'option_name' => ':name',
                'option_value' => ':value',
            ])
            ->setParameters([
                'name' => $optionName,
                'value' => (string) $optionValue,
            ])
            ->executeStatement()
        ;

        return $affectedRows > 0;
    }

    public function update(string $optionName, mixed $optionValue): bool
    {
        if (is_array($optionValue)) {
            $optionValue = serialize($optionValue);
        }

        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->update($this->entityManager->getTablesPrefix() . 'options')
            ->set('option_value', ':value')
            ->where('option_name = :name')
            ->setParameters([
                'name' => $optionName,
                'value' => (string) $optionValue,
            ])
            ->executeStatement()
        ;

        return $affectedRows > 0;
    }

    public function delete(string $optionName): bool
    {
        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->delete($this->entityManager->getTablesPrefix() . 'options')
            ->where('option_name = :name')
            ->setParameters([
                'name' => $optionName,
            ])
            ->executeStatement()
        ;

        return $affectedRows > 0;
    }
}

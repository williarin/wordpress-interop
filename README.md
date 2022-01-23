# WordPress Interop

## Introduction

This library aims to simplify the interaction with WordPress databases through third-party applications.
It relies on Doctrine DBAL and looks like Doctrine ORM.

It can perform simple tasks out of the box such as querying posts, retrieving attachment data, etc.

You can extend it by adding your own repositories and querying methods.

## Installation

This library can be used as standalone:
```bash
composer require williarin/wordpress-interop
```

Or with Symfony:
```bash
composer require williarin/wordpress-interop-bundle
```

Find the documentation for the Symfony bundle on [the dedicated repository](https://github.com/williarin/wordpress-standalone-bundle) page.

## Usage

### Overview

```php
$post = $manager->getRepository(Post::class)->find(15);
```

### In detail

The first thing to do is to create an entity manager linked to your DBAL connection targeting your WordPress database.

```php
$connection = DriverManager::getConnection(['url' => 'mysql://user:pass@localhost:3306/wp_mywebsite?serverVersion=8.0']);

$objectNormalizer = new ObjectNormalizer(
    new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())),
    new CamelCaseToSnakeCaseNameConverter(),
    null,
    new ReflectionExtractor()
);

$serializer = new Serializer([
    new DateTimeNormalizer(),
    new ArrayDenormalizer(),
    new SerializedArrayDenormalizer($objectNormalizer),
    $objectNormalizer,
]);

$manager = new EntityManager($connection, $serializer);
```

Then you can query the database:
```php
/** @var PostRepository $postRepository */
$postRepository = $manager->getRepository(Post::class);
$myPost = $postRepository->find(15);
$allPosts = $postRepository->findAll();
```

## Create your own repositories

Say you have a custom post type named `project`.

First you create a simple entity:

```php
// App/Wordpress/Entity/Project.php
namespace App\Wordpress\Entity;

use App\Wordpress\Repository\ProjectRepository;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;

#[RepositoryClass(ProjectRepository::class)]
final class Project extends BaseEntity
{
}
```
Then a repository:
```php
// App/Wordpress/Repository/ProjectRepository.php
namespace App\Wordpress\Repository;

use App\Wordpress\Entity\Project;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Repository\EntityRepository;

/**
 * @method Project|null find($id)
 * @method Project[]    findAll()
 */
final class ProjectRepository extends EntityRepository
{
    protected const POST_TYPE = 'project';

    public function __construct(protected EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        parent::__construct($entityManager, $serializer, Project::class);
    }
    
    // Add your own methods here
}
```
Then use it like this:
```php
$allProjects = $manager->getRepository(Project::class)->findAll();
```

## License

[MIT](LICENSE)

Copyright (c) 2022, William Arin

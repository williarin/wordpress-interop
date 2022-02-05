# WordPress Interop

## Introduction

This library aims to simplify the interaction with WordPress databases through third-party applications.
It relies on Doctrine DBAL and looks like Doctrine ORM.

It can perform simple tasks out of the box such as querying posts, retrieving attachment data, etc.

You can extend it by adding your own repositories and querying methods.

**Warning!** Although it looks like an ORM, it's not an ORM library. It doesn't have two-way data manipulation features.
See this as a simple WordPress database manipulation helper library.

## Installation

This library can be used as standalone:
```bash
composer require williarin/wordpress-interop
```

Or with Symfony:
```bash
composer require williarin/wordpress-interop-bundle
```

Find the documentation for the Symfony bundle on [the dedicated repository](https://github.com/williarin/wordpress-interop-bundle) page.

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

## Documentation

### Basic post querying

This works with any entity inherited from `BaseEntity`.
Built-in entities are `Post`, `Page`, `Attachment` and `Product` but you can [create your own](#create-your-own-repositories).

```php
// Fetch a post by ID
$post = $manager->getRepository(Post::class)->find(1);

// Fetch the latest published post
$post = $manager->getRepository(Post::class)
    ->findOneByPostStatus('publish', ['post_date' => 'DESC']);

// Fetch the latest published post which has 1 comment
$post = $manager->getRepository(Post::class)
    ->findOneBy(
        ['post_status' => 'publish', 'comment_count' => 1],
        ['post_date' => 'DESC'],
    );

// Fetch the latest published post which has the most comments
$post = $manager->getRepository(Post::class)
    ->findOneByPostStatus(
        'publish',
        ['comment_count' => 'DESC', 'post_date' => 'DESC'],
    );

// All posts
$posts = $manager->getRepository(Post::class)->findAll();

// All private posts
$posts = $manager->getRepository(Post::class)->findByPostStatus('private');
```

### EAV querying

The query system supports directly querying EAV attributes.
However, it only works with properties that have been declared in the corresponding entity.

In the example below, `sku` and `stock_status` are attributes from `wp_postmeta` table.

```php
// Fetch a product by its SKU
$product = $manager->getRepository(Product::class)->findOneBySku('woo-vneck-tee');

// Fetch the latest published product which is in stock
$product = $manager->getRepository(Product::class)
    ->findOneBy(
        ['stock_status' => 'instock', 'post_status' => 'publish'],
        ['post_date' => 'DESC'],
    );
    
// Fetch all published products which are in stock
$products = $manager->getRepository(Product::class)
    ->findBy(
        ['stock_status' => 'instock', 'post_status' => 'publish'],
        ['post_date' => 'DESC'],
    );
```

### Field update
There's a type validation before update.
You can't assign a string to a date field, a string to an int field, etc.

```php
$repository = $manager->getRepository(Post::class);
$repository->updatePostTitle(4, 'New title');
$repository->updatePostContent(4, 'New content');
$repository->updatePostDate(4, new \DateTime());
// Alternative
$repository->updateSingleField(4, 'post_status', 'publish');
```

### Available entities and repositories

* `Post` and `PostRepository`
* `Page` and `PageRepository`
* `Attachment` and `AttachmentRepository`
* `Product` and `ProductRepository` (WooCommerce)
* `Option` and `OptionRepository`
* `PostMeta` and `PostMetaRepository`

### Get an option value

To retrieve a WordPress option, you have several choices:
```php
// Query the option name yourself
$blogName = $manager->getRepository(Option::class)->find('blogname');

// Use a predefined getter
$blogName = $manager->getRepository(Option::class)->findBlogName();

// If there isn't a predefined getter, use a magic method.
// Here we get the 'active_plugins' option, automatically unserialized.
$plugins = $manager->getRepository(Option::class)->findActivePlugins();
```

### Create your own repositories

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

use App\Wordpress\Entity\Project;use Symfony\Component\Serializer\SerializerInterface;use Williarin\WordpressInterop\Bridge\Repository\AbstractEntityRepository;use Williarin\WordpressInterop\EntityManagerInterface;

/**
 * @method Project|null find($id)
 * @method Project[]    findAll()
 */
final class ProjectRepository extends AbstractEntityRepository
{
    public function __construct(/* inject additional services if you need them */)
    {
        parent::__construct(Project::class);
    }
    
    protected function getPostType(): string
    {
        return 'project';
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

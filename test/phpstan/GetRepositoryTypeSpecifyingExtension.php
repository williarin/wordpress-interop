<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\PHPStan;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Williarin\WordpressInterop\EntityManager;

class GetRepositoryTypeSpecifyingExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return EntityManager::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'getRepository';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type
    {
        /*
            @TODO
            1. $entityClassName = $args[0]->value
            2. read class attributes from reflection
            3. set return type to 1st parameter of RepositoryClass attribute: #[RepositoryClass(TermRepository::class)]
        */

        // Hard-code finding Repository name from Entity name
        $entityClass = $methodCall->getArgs()[0]->value;
        $className = $entityClass->class;
        if ($entityClass->class instanceof \PhpParser\Node\Name\FullyQualified) {
            $className = $entityClass->class->toString();
        }

        return new ObjectType(str_replace('\\Entity\\', '\\Repository\\', $className).'Repository');
    }
}

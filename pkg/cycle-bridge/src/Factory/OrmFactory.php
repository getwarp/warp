<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Factory;

use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\MapperInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\Relation\RelationInterface;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Select\LoaderInterface;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Select\Source;
use Cycle\ORM\Select\SourceInterface;
use spaceonfire\Bridge\Cycle\Collection\Doctrine\DoctrineCollectionFactory;
use spaceonfire\Bridge\Cycle\Collection\Relation\SchemaExtra;
use Spiral\Core\Container;
use Spiral\Core\Container\Autowire;
use Spiral\Core\FactoryInterface as CoreFactory;
use Spiral\Database\DatabaseInterface;
use Spiral\Database\DatabaseProviderInterface;

final class OrmFactory implements FactoryInterface
{
    /**
     * @var RelationConfig<array-key,array<array-key,class-string>>
     */
    private RelationConfig $config;

    private CoreFactory $factory;

    private DatabaseProviderInterface $dbal;

    /**
     * @var array<array-key,class-string|null>
     */
    private array $defaults = [
        SchemaInterface::REPOSITORY => Repository::class,
        SchemaInterface::SOURCE => Source::class,
        SchemaInterface::MAPPER => Mapper::class,
        SchemaInterface::SCOPE => null,
        SchemaExtra::COLLECTION_FACTORY => DoctrineCollectionFactory::class,
    ];

    /**
     * @param DatabaseProviderInterface $dbal
     * @param RelationConfig<array-key,array<array-key,class-string>>|null $config
     * @param CoreFactory|null $factory
     */
    public function __construct(
        DatabaseProviderInterface $dbal,
        ?RelationConfig $config = null,
        ?CoreFactory $factory = null
    ) {
        $this->dbal = $dbal;
        $this->config = $config ?? SchemaExtra::getRelationConfig();
        $this->factory = $factory ?? new Container();
    }

    /**
     * @template T
     * @param class-string<T> $alias
     * @param array<string,mixed> $parameters
     * @return T
     */
    public function make(string $alias, array $parameters = [])
    {
        return $this->factory->make($alias, $parameters);
    }

    public function mapper(
        ORMInterface $orm,
        SchemaInterface $schema,
        string $role
    ): MapperInterface {
        $mapper = $this->getSchemaService($schema, $role, SchemaInterface::MAPPER);

        \assert(null !== $mapper);

        return $mapper->resolve($this, [
            'orm' => $orm,
            'role' => $role,
            'schema' => $schema->define($role, SchemaInterface::SCHEMA),
        ]);
    }

    public function loader(
        ORMInterface $orm,
        SchemaInterface $schema,
        string $role,
        string $relation
    ): LoaderInterface {
        $relationSchema = $schema->defineRelation($role, $relation);

        return $this->config->getLoader($relationSchema[Relation::TYPE])->resolve($this, [
            'orm' => $orm,
            'name' => $relation,
            'target' => $relationSchema[Relation::TARGET],
            'schema' => $relationSchema[Relation::SCHEMA],
        ]);
    }

    public function relation(
        ORMInterface $orm,
        SchemaInterface $schema,
        string $role,
        string $relation
    ): RelationInterface {
        $entitySchema = $schema->define($role, SchemaInterface::SCHEMA);
        $relationSchema = $schema->defineRelation($role, $relation);
        $relationSchemaExtra = ($entitySchema[SchemaExtra::RELATION_EXTRA] ?? [])[$relation] ?? [];

        $relationSchema =
            [
                Relation::TYPE => $relationSchema[Relation::TYPE],
                Relation::TARGET => $relationSchema[Relation::TARGET],
                Relation::LOAD => $relationSchema[Relation::LOAD] ?? null,
            ]
            + $relationSchema[Relation::SCHEMA]
            + $relationSchemaExtra;

        $def = $this->collectionFactory($relationSchema);
        $collectionFactory = null === $def ? null : $def->resolve($this, [
            'orm' => $orm,
            'schema' => $schema,
            'role' => $role,
            'relation' => $relation,
        ]);

        return $this->config->getRelation($relationSchema[Relation::TYPE])->resolve($this, [
            'orm' => $orm,
            'name' => $relation,
            'target' => $relationSchema[Relation::TARGET],
            'schema' => $relationSchema,
            'collectionFactory' => $collectionFactory,
        ]);
    }

    public function database(?string $database = null): DatabaseInterface
    {
        return $this->dbal->database($database);
    }

    /**
     * @param ORMInterface $orm
     * @param SchemaInterface $schema
     * @param string $role
     * @param Select<object>|null $select
     * @return RepositoryInterface
     */
    public function repository(
        ORMInterface $orm,
        SchemaInterface $schema,
        string $role,
        ?Select $select
    ): RepositoryInterface {
        $def = $this->getSchemaService($schema, $role, SchemaInterface::REPOSITORY);

        \assert(null !== $def);

        return $def->resolve($this, [
            'select' => $select,
            'orm' => $orm,
            'role' => $role,
        ]);
    }

    public function source(
        ORMInterface $orm,
        SchemaInterface $schema,
        string $role
    ): SourceInterface {
        $source = $this->getSchemaService($schema, $role, SchemaInterface::SOURCE);

        \assert(null !== $source);

        $source = $source->resolve($this, [
            'orm' => $orm,
            'role' => $role,
            'database' => $this->database($schema->define($role, SchemaInterface::DATABASE)),
            'table' => $schema->define($role, SchemaInterface::TABLE),
        ]);

        \assert($source instanceof SourceInterface);

        $scope = $this->getSchemaService($schema, $role, SchemaInterface::SCOPE);

        if (null === $scope) {
            return $source;
        }

        return $source->withConstrain($scope->resolve($this));
    }

    /**
     * @param array<array-key,class-string|null> $defaults
     * @return self
     */
    public function withDefaultSchemaClasses(array $defaults): self
    {
        $clone = clone $this;

        $clone->defaults = $defaults + $this->defaults;

        return $clone;
    }

    /**
     * @param array<array-key,mixed> $relationSchema
     * @return Autowire|null
     */
    private function collectionFactory(array $relationSchema): ?Autowire
    {
        $collectionFactoryClass = $relationSchema[SchemaExtra::COLLECTION_FACTORY]
            ?? $this->defaults[SchemaExtra::COLLECTION_FACTORY]
            ?? null;

        if (null === $collectionFactoryClass) {
            return null;
        }

        return Autowire::wire($collectionFactoryClass);
    }

    /**
     * @param SchemaInterface $schema
     * @param string $role
     * @param int $service
     * @return Autowire|null
     */
    private function getSchemaService(SchemaInterface $schema, string $role, int $service): ?Autowire
    {
        $class = $schema->define($role, $service) ?? $this->defaults[$service] ?? null;

        if (null === $class) {
            return null;
        }

        return Autowire::wire($class);
    }
}

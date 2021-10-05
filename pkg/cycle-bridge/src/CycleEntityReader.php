<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Promise\ReferenceInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use spaceonfire\Bridge\Cycle\Select\CriteriaScope;
use spaceonfire\Bridge\Cycle\Select\LazySelectIterator;
use spaceonfire\Bridge\Cycle\Select\PrimaryBuilder;
use spaceonfire\Bridge\Cycle\Select\ScopeAggregate;
use spaceonfire\Collection\Collection;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Collection\Iterator\ArrayCacheIterator;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\DefaultEntityNotFoundExceptionFactory;
use spaceonfire\DataSource\EntityNotFoundExceptionFactoryInterface;
use spaceonfire\DataSource\EntityReaderInterface;
use spaceonfire\Type\InstanceOfType;
use spaceonfire\Type\TypeInterface;

/**
 * @template E of object
 * @implements EntityReaderInterface<E>
 */
final class CycleEntityReader implements EntityReaderInterface
{
    private ORMInterface $orm;

    private string $role;

    private PrimaryBuilder $primaryBuilder;

    /**
     * @var class-string<E>|null
     */
    private ?string $classname;

    private EntityNotFoundExceptionFactoryInterface $notFoundExceptionFactory;

    /**
     * @param ORMInterface $orm
     * @param string|class-string<E> $role
     * @phpstan-param class-string<E> $role
     * @param EntityNotFoundExceptionFactoryInterface|null $notFoundExceptionFactory
     */
    public function __construct(
        ORMInterface $orm,
        string $role,
        ?EntityNotFoundExceptionFactoryInterface $notFoundExceptionFactory = null
    ) {
        $this->orm = $orm;
        $this->role = $this->orm->resolveRole($role);
        $this->primaryBuilder = new PrimaryBuilder($this->orm, $this->role);
        $this->classname = $this->resolveEntityClass($role);
        $this->notFoundExceptionFactory = $notFoundExceptionFactory ?? new DefaultEntityNotFoundExceptionFactory();
    }

    public function findByPrimary($primary, ?CriteriaInterface $criteria = null): object
    {
        $primaryBuilder = $this->primaryBuilder->withScope($primary);

        /** @phpstan-var E|null $entity */
        $entity = $this->findReferenceInHeap($primaryBuilder->getReference())
            ?? $this->findOne($primaryBuilder->getCriteria($criteria));

        if (null === $entity) {
            throw $this->notFoundExceptionFactory->make($this->classname ?? $this->role, $primary);
        }

        return $entity;
    }

    public function findAll(?CriteriaInterface $criteria = null): CollectionInterface
    {
        return Collection::new(
            ArrayCacheIterator::wrap(new LazySelectIterator($this->makeSelect($criteria))),
            $this->getEntityType()
        );
    }

    public function findOne(?CriteriaInterface $criteria = null): ?object
    {
        $criteria = ($criteria ?? Criteria::new())->limit(1)->offset(0);

        /** @phpstan-var E|null $entity */
        $entity = $this->makeSelect($criteria)->fetchOne();

        return $entity ?? null;
    }

    public function count(?CriteriaInterface $criteria = null): int
    {
        if (null !== $criteria) {
            $criteria = $criteria->limit(null)->offset(null);
        }

        return $this->makeSelect($criteria)->count();
    }

    /**
     * @param CriteriaInterface|null $criteria
     * @return Select<E>
     */
    private function makeSelect(?CriteriaInterface $criteria = null): Select
    {
        $select = new Select($this->orm, $this->role);

        $scope = new ScopeAggregate();

        if (null !== $sourceScope = $this->orm->getSource($this->role)->getConstrain()) {
            $scope->add($sourceScope);
        }

        if (null !== $criteria) {
            $scope->add(new CriteriaScope($criteria, $this->orm, $this->role));

            // TODO: if only this can be done in CriteriaScope through QueryBuilder.
            $select->load($criteria->getInclude());
        }

        $select->scope($scope);

        return $select;
    }

    private function getEntityType(): ?TypeInterface
    {
        return null === $this->classname ? null : InstanceOfType::new($this->classname);
    }

    private function findReferenceInHeap(ReferenceInterface $reference): ?object
    {
        return $this->orm->get($reference->__role(), $reference->__scope(), false);
    }

    /**
     * @param string|class-string<E> $role
     * @return class-string<E>|null
     */
    private function resolveEntityClass(string $role): ?string
    {
        if (\class_exists($role)) {
            /** @phpstan-var class-string<E> $role */
            return $role;
        }

        $class = $this->orm->getSchema()->define($role, SchemaInterface::ENTITY);
        if (\is_string($class) && \class_exists($class)) {
            /** @phpstan-var class-string<E> $class */
            return $class;
        }

        return null;
    }
}

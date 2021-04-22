<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Promise\Reference;
use Cycle\ORM\Promise\ReferenceInterface;
use Cycle\ORM\SchemaInterface;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\Criteria\Expression\ExpressionFactory;

/**
 * @internal
 */
final class PrimaryBuilder
{
    private string $role;

    /**
     * @var string[]
     */
    private array $primaryKey;

    /**
     * @var array<string,mixed>|null
     */
    private ?array $scope = null;

    private ?ReferenceInterface $reference = null;

    public function __construct(ORMInterface $orm, string $role)
    {
        $this->role = $orm->resolveRole($role);
        $this->primaryKey = self::getPrimaryKeys($orm, $this->role);
    }

    /**
     * @param mixed $scope
     * @return $this
     */
    public function withScope($scope): self
    {
        [$scope, $ref] = $this->prepareScope($scope);
        $clone = clone $this;
        $clone->scope = $scope;
        $clone->reference = $ref;
        return $clone;
    }

    public function getCriteria(?CriteriaInterface $criteria = null): CriteriaInterface
    {
        if (null === $this->scope) {
            throw new \LogicException('You should provide scope by calling withScope().');
        }

        $ef = ExpressionFactory::new();
        $criteria ??= Criteria::new();

        foreach ($this->scope as $offset => $value) {
            $criteria = $criteria->andWhere($ef->property($offset, $ef->same($value)));
        }

        return $criteria;
    }

    public function getReference(): ReferenceInterface
    {
        if (null === $this->scope) {
            throw new \LogicException('You should provide scope by calling withScope().');
        }

        return $this->reference ??= new Reference($this->role, $this->scope);
    }

    /**
     * @param mixed $scope
     * @return array{array<string,mixed>,ReferenceInterface|null}
     */
    private function prepareScope($scope): array
    {
        if ($scope instanceof ReferenceInterface) {
            if ($this->role !== $scope->__role()) {
                throw new \LogicException(
                    \sprintf('Expected that reference targets to %s. Got: %s.', $this->role, $scope->__role())
                );
            }

            return [$scope->__scope(), $scope];
        }

        $output = [];

        $scope = \is_array($scope) ? $scope : [$scope];
        $pk = \array_combine($this->primaryKey, $this->primaryKey);

        foreach ($scope as $offset => $value) {
            if (\is_int($offset)) {
                $key = \array_shift($pk);
                $output[$key] = $value;
                continue;
            }

            if (isset($pk[$offset])) {
                $output[$offset] = $value;
                unset($pk[$offset]);
            }
        }

        if ([] === $output) {
            throw new \LogicException(\sprintf('Cannot extract scope from %s.', \get_debug_type($scope)));
        }

        return [$output, null];
    }

    /**
     * @param ORMInterface $orm
     * @param string $role
     * @return string[]
     */
    private static function getPrimaryKeys(ORMInterface $orm, string $role): array
    {
        $pk = $orm->getSchema()->define($role, SchemaInterface::PRIMARY_KEY);
        $pk = \is_array($pk) ? $pk : [$pk];
        return \array_map(static fn ($v) => (string)$v, $pk);
    }
}

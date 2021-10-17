<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Schema;

use Cycle\Schema\Definition\Entity;

/**
 * @see Entity
 * @phpstan-import-type FieldShape from FieldDto
 * @phpstan-import-type RelationShape from RelationDto
 * @phpstan-type EntityShape=array{role?:string,database?:string,table:string,class?:class-string,mapper?:class-string,source?:class-string,scope?:class-string,repository?:class-string,fields?:FieldShape[],relations?:RelationShape[],schema?:array<array-key,mixed>,options?:array<array-key,mixed>}
 * @phpstan-type EntityChildrenShape=array{children?:EntityShape[]}
 */
final class EntityDto
{
    public const ROLE = 'role';

    public const DATABASE = 'database';

    public const TABLE = 'table';

    public const CLASS_NAME = 'class';

    public const MAPPER = 'mapper';

    public const SOURCE = 'source';

    public const SCOPE = 'scope';

    public const REPOSITORY = 'repository';

    public const FIELDS = 'fields';

    public const RELATIONS = 'relations';

    public const SCHEMA = 'schema';

    public const OPTIONS = 'options';

    public const CHILDREN = 'children';

    /**
     * @param EntityShape $data
     * @return Entity
     */
    public static function makeSchema(array $data): Entity
    {
        $entity = new Entity();

        // TODO: assert argument types
        if (isset($data[self::ROLE])) {
            $entity->setRole($data[self::ROLE]);
        }
        if (isset($data[self::CLASS_NAME])) {
            $entity->setClass($data[self::CLASS_NAME]);
        }
        if (isset($data[self::MAPPER])) {
            $entity->setMapper($data[self::MAPPER]);
        }
        if (isset($data[self::SOURCE])) {
            $entity->setSource($data[self::SOURCE]);
        }
        if (isset($data[self::SCOPE])) {
            $entity->setConstrain($data[self::SCOPE]);
        }
        if (isset($data[self::REPOSITORY])) {
            $entity->setRepository($data[self::REPOSITORY]);
        }
        if (isset($data[self::SCHEMA])) {
            $entity->setSchema($data[self::SCHEMA]);
        }

        foreach ($data[self::OPTIONS] ?? [] as $option => $value) {
            $entity->getOptions()->set($option, $value);
        }

        foreach ($data[self::FIELDS] ?? [] as $field) {
            $entity->getFields()->set($field[FieldDto::NAME], FieldDto::makeSchema($field));
        }

        foreach ($data[self::RELATIONS] ?? [] as $relation) {
            $entity->getRelations()->set($relation[RelationDto::NAME], RelationDto::makeSchema($relation));
        }

        return $entity;
    }

    /**
     * @param EntityChildrenShape $data
     * @return \Generator<Entity>
     */
    public static function makeChildren(array $data): \Generator
    {
        foreach ($data[self::CHILDREN] ?? [] as $child) {
            yield self::makeSchema($child);
        }
    }
}

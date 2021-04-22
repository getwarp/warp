<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Schema;

use Cycle\Schema\Definition\Relation;
use Cycle\Schema\Generator\GenerateRelations;

/**
 * @see Relation
 * @see GenerateRelations
 * @phpstan-type RelationType string
 * @phpstan-type RelationOptionsShape=array{nullable?:boolean,cascade?:boolean,load?:int|null,innerKey?:string,outerKey?:string,morphKey?:string,through?:string,throughInnerKey?:string,throughOuterKey?:string,throughWhere?:array<string,mixed>,where?:array<string,mixed>,orderBy?:array<string,string>,fkCreate?:boolean,fkAction?:string,indexCreate?:boolean,morphKeyLength?:int}
 * @phpstan-type RelationShape=array{name:string,target?:string,type?:RelationType,inverse?:string,inverseType?:RelationType,inverseLoad?:int,options?:RelationOptionsShape}
 */
final class RelationDto
{
    public const NAME = 'name';

    public const TARGET = 'target';

    public const TYPE = 'type';

    public const TYPE_EMBEDDED = 'embedded';

    public const TYPE_BELONGS_TO = 'belongsTo';

    public const TYPE_HAS_ONE = 'hasOne';

    public const TYPE_HAS_MANY = 'hasMany';

    public const TYPE_REFERS_TO = 'refersTo';

    public const TYPE_MANY_TO_MANY = 'manyToMany';

    public const TYPE_BELONGS_TO_MORPHED = 'belongsToMorphed';

    public const TYPE_MORPHED_HAS_ONE = 'morphedHasOne';

    public const TYPE_MORPHED_HAS_MANY = 'morphedHasMany';

    public const INVERSE_NAME = 'inverse';

    public const INVERSE_TYPE = 'inverseType';

    public const INVERSE_LOAD = 'inverseLoad';

    public const OPTIONS = 'options';

    public const OPTION_NULLABLE = 'nullable';

    public const OPTION_CASCADE = 'cascade';

    public const OPTION_LOAD = 'load';

    public const OPTION_INNER_KEY = 'innerKey';

    public const OPTION_OUTER_KEY = 'outerKey';

    public const OPTION_MORPH_KEY = 'morphKey';

    public const OPTION_THROUGH = 'through';

    public const OPTION_THROUGH_INNER_KEY = 'throughInnerKey';

    public const OPTION_THROUGH_OUTER_KEY = 'throughOuterKey';

    public const OPTION_THROUGH_WHERE = 'throughWhere';

    public const OPTION_WHERE = 'where';

    public const OPTION_ORDER_BY = 'orderBy';

    public const OPTION_FK_CREATE = 'fkCreate';

    public const OPTION_FK_ACTION = 'fkAction';

    public const OPTION_INDEX_CREATE = 'indexCreate';

    public const OPTION_MORPH_KEY_LENGTH = 'morphKeyLength';

    public const LOAD_PROMISE = \Cycle\ORM\Relation::LOAD_PROMISE;

    public const LOAD_EAGER = \Cycle\ORM\Relation::LOAD_EAGER;

    /**
     * @param RelationShape $data
     * @return Relation
     */
    public static function makeSchema(array $data): Relation
    {
        $relation = new Relation();

        // TODO: assert argument types
        if (isset($data[self::TARGET])) {
            $relation->setTarget($data[self::TARGET]);
        }
        if (isset($data[self::TYPE])) {
            $relation->setType($data[self::TYPE]);
        }
        if (isset($data[self::INVERSE_NAME])) {
            \assert(isset($data[self::INVERSE_TYPE]));
            $relation->setInverse(
                $data[self::INVERSE_NAME],
                $data[self::INVERSE_TYPE],
                $data[self::INVERSE_LOAD] ?? null,
            );
        }

        foreach ($data[self::OPTIONS] ?? [] as $option => $value) {
            $relation->getOptions()->set($option, $value);
        }

        return $relation;
    }
}

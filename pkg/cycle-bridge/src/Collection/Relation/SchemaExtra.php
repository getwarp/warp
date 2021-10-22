<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection\Relation;

use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Relation;
use Cycle\ORM\Select\Loader;

abstract class SchemaExtra
{
    public const RELATION_EXTRA = 'RELATION_EXTRA';

    public const COLLECTION_FACTORY = 'COLLECTION_FACTORY';

    /**
     * @return RelationConfig<array-key,array<array-key,class-string>>
     */
    public static function getRelationConfig(): RelationConfig
    {
        return new RelationConfig([
            Relation::EMBEDDED => [
                RelationConfig::LOADER => Loader\EmbeddedLoader::class,
                RelationConfig::RELATION => Relation\Embedded::class,
            ],
            Relation::HAS_ONE => [
                RelationConfig::LOADER => Loader\HasOneLoader::class,
                RelationConfig::RELATION => Relation\HasOne::class,
            ],
            Relation::BELONGS_TO => [
                RelationConfig::LOADER => Loader\BelongsToLoader::class,
                RelationConfig::RELATION => Relation\BelongsTo::class,
            ],
            Relation::REFERS_TO => [
                RelationConfig::LOADER => Loader\BelongsToLoader::class,
                RelationConfig::RELATION => Relation\RefersTo::class,
            ],
            Relation::HAS_MANY => [
                RelationConfig::LOADER => Loader\HasManyLoader::class,
                RelationConfig::RELATION => HasMany::class,
            ],
            Relation::MANY_TO_MANY => [
                RelationConfig::LOADER => Loader\ManyToManyLoader::class,
                RelationConfig::RELATION => ManyToMany::class,
            ],
            Relation::MORPHED_HAS_ONE => [
                RelationConfig::LOADER => Loader\Morphed\MorphedHasOneLoader::class,
                RelationConfig::RELATION => Relation\Morphed\MorphedHasOne::class,
            ],
            Relation::MORPHED_HAS_MANY => [
                RelationConfig::LOADER => Loader\Morphed\MorphedHasManyLoader::class,
                RelationConfig::RELATION => MorphedHasMany::class,
            ],
            Relation::BELONGS_TO_MORPHED => [
                RelationConfig::RELATION => Relation\Morphed\BelongsToMorphed::class,
            ],
        ]);
    }
}

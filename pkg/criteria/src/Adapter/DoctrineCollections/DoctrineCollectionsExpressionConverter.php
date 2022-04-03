<?php

declare(strict_types=1);

namespace Warp\Criteria\Adapter\DoctrineCollections;

use function class_alias;

class_alias(
    \Warp\Criteria\Bridge\DoctrineCollections\DoctrineCollectionsExpressionConverter::class,
    __NAMESPACE__ . '\DoctrineCollectionsExpressionConverter'
);

if (false) {
    /**
     * Converts Expressions from Doctrine collections to webmozart expressions
     * @deprecated Will be dropped in next major release.
     * Use \Warp\Criteria\Bridge\DoctrineCollections\DoctrineCollectionsExpressionConverter instead.
     */
    class DoctrineCollectionsExpressionConverter extends \Warp\Criteria\Bridge\DoctrineCollections\DoctrineCollectionsExpressionConverter
    {
    }
}

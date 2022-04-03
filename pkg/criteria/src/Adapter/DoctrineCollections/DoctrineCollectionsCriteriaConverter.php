<?php

declare(strict_types=1);

namespace Warp\Criteria\Adapter\DoctrineCollections;

use function class_alias;

class_alias(
    \Warp\Criteria\Bridge\DoctrineCollections\DoctrineCollectionsCriteriaConverter::class,
    __NAMESPACE__ . '\DoctrineCollectionsCriteriaConverter'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\Criteria\Bridge\DoctrineCollections\DoctrineCollectionsCriteriaConverter instead.
     */
    class DoctrineCollectionsCriteriaConverter extends \Warp\Criteria\Bridge\DoctrineCollections\DoctrineCollectionsCriteriaConverter
    {
    }
}

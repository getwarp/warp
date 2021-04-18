<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Adapter\DoctrineCollections;

use function class_alias;

class_alias(
    \spaceonfire\Criteria\Bridge\DoctrineCollections\DoctrineCollectionsCriteriaConverter::class,
    __NAMESPACE__ . '\DoctrineCollectionsCriteriaConverter'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\Criteria\Bridge\DoctrineCollections\DoctrineCollectionsCriteriaConverter instead.
     */
    class DoctrineCollectionsCriteriaConverter extends \spaceonfire\Criteria\Bridge\DoctrineCollections\DoctrineCollectionsCriteriaConverter
    {
    }
}

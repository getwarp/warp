<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

/**
 * @template E of object
 * @extends EntityReaderInterface<E>
 * @extends EntityPersisterInterface<E>
 */
interface RepositoryInterface extends EntityReaderInterface, EntityPersisterInterface
{
}

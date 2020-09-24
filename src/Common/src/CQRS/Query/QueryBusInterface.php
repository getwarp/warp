<?php

declare(strict_types=1);

namespace spaceonfire\Common\CQRS\Query;

interface QueryBusInterface
{
    /**
     * Asks for the result of the query.
     * @param QueryInterface $query
     * @return ResponseInterface|null
     */
    public function ask(QueryInterface $query): ?ResponseInterface;
}

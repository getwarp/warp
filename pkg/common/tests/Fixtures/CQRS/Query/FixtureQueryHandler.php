<?php

declare(strict_types=1);

namespace spaceonfire\Common\Fixtures\CQRS\Query;

final class FixtureQueryHandler
{
    private bool $returnNull;

    public function __construct(bool $returnNull = false)
    {
        $this->returnNull = $returnNull;
    }

    public function __invoke(FixtureQuery $query): ?FixtureQueryResponse
    {
        if ($this->returnNull) {
            return null;
        }

        return new FixtureQueryResponse();
    }
}

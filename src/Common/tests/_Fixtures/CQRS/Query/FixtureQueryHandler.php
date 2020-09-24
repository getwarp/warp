<?php

declare(strict_types=1);

namespace spaceonfire\Common\_Fixtures\CQRS\Query;

final class FixtureQueryHandler
{
    public function __invoke(FixtureQuery $query): FixtureQueryResponse
    {
        return new FixtureQueryResponse();
    }
}

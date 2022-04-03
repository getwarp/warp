<?php

declare(strict_types=1);

namespace Warp\DataSource;

use PHPUnit\Framework\TestCase;
use Warp\DataSource\Fixtures\Domain\Post\Post;
use Warp\DataSource\Fixtures\Infrastructure\Mapper\StubMapper;
use Warp\DataSource\Fixtures\Infrastructure\Persistence\Post\InMemoryPostRepository;
use Warp\DataSource\Traits\RepositoryTestTrait;

class RepositoryTest extends TestCase
{
    use RepositoryTestTrait;

    protected function setUp(): void
    {
        if (self::$repository === null) {
            self::$repository = new InMemoryPostRepository([
                1 => new Post(
                    '0de0e289-28a7-4944-953a-079d09ac4865',
                    'Hello, World!',
                    '35a60006-c34a-4c0b-8e9d-7759f6d0c09b'
                ),
                2 => new Post(
                    'be1cc1cc-0389-49c5-bb60-1368141b48b3',
                    'New Post',
                    '35a60006-c34a-4c0b-8e9d-7759f6d0c09b'
                ),
            ]);
        }
    }

    public function testGetMapper(): void
    {
        self::assertInstanceOf(StubMapper::class, self::$repository->getMapper());
    }
}

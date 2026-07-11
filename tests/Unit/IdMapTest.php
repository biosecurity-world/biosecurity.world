<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\IdMap;
use PHPUnit\Framework\TestCase;

final class IdMapTest extends TestCase
{
    protected function tearDown(): void
    {
        IdMap::restore([]);
    }

    public function test_restoring_a_map_advances_the_id_counter(): void
    {
        IdMap::restore(['first' => 2, 'second' => 7]);

        self::assertSame(2, IdMap::hash('first'));
        self::assertSame(8, IdMap::hash('new'));
        self::assertSame('new', IdMap::find(8));
    }

    public function test_hashing_the_same_external_id_is_stable(): void
    {
        IdMap::restore([]);

        $first = IdMap::hash('notion-id');

        self::assertSame($first, IdMap::hash('notion-id'));
    }
}

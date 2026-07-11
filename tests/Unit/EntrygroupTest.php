<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\NotionData\Enums\DomainEnum;
use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\Models\Entrygroup;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

final class EntrygroupTest extends TestCase
{
    public function test_it_counts_each_domain_and_ignores_invalid_lookup_values(): void
    {
        $group = new Entrygroup(20, [1, 2, 3, 404]);
        $lookup = [
            1 => $this->entryWithDomains(DomainEnum::Technical),
            2 => $this->entryWithDomains(DomainEnum::Governance),
            3 => $this->entryWithDomains(DomainEnum::Technical, DomainEnum::Governance),
            404 => $group,
        ];

        self::assertSame(['tech' => 2, 'gov' => 2], $group->countDomains($lookup));
        self::assertSame(2, $group->countTechnical($lookup));
        self::assertSame(2, $group->countGovernance($lookup));
    }

    private function entryWithDomains(DomainEnum ...$domains): Entry
    {
        $reflection = new \ReflectionClass(Entry::class);
        /** @var Entry $entry */
        $entry = $reflection->newInstanceWithoutConstructor();
        $entry->domains = new Collection(array_values($domains));

        return $entry;
    }
}

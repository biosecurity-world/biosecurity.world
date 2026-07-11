<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\NotionData\Models\Category;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CategoryTest extends TestCase
{
    /** @return iterable<string, array{int, int, ?string, int, ?string}> */
    public static function domainCounts(): iterable
    {
        yield 'empty category' => [0, 0, null, 0, null];
        yield 'technical majority' => [3, 1, 'technical', 75, '75% technical'];
        yield 'governance majority' => [1, 3, 'governance', 75, '75% governance'];
        yield 'tie favors technical' => [2, 2, 'technical', 50, '50% technical'];
    }

    #[DataProvider('domainCounts')]
    public function test_it_summarizes_domain_counts(
        int $technical,
        int $governance,
        ?string $expectedDomain,
        int $expectedPercentage,
        ?string $expectedDisplay,
    ): void {
        $category = new Category(
            id: 1,
            parentId: null,
            label: 'Category',
            createdAt: new \DateTimeImmutable,
            technicalEntriesCount: $technical,
            governanceEntriesCount: $governance,
        );

        self::assertSame($technical + $governance, $category->totalEntriesCount());
        self::assertSame($expectedDomain, $category->dominantDomain());
        self::assertSame($expectedPercentage, $category->dominantDomainPercentage());
        self::assertSame($expectedDisplay, $category->dominantDomainDisplay());
    }
}

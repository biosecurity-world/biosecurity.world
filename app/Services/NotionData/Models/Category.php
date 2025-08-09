<?php

declare(strict_types=1);

namespace App\Services\NotionData\Models;

class Category
{
    /**
     * @param int $technicalEntriesCount Number of entries in this category's subtree in the Technical domain
     * @param int $governanceEntriesCount Number of entries in this category's subtree in the Governance domain
     */
    public function __construct(
        public int $id,
        public ?int $parentId,
        public string $label,
        public \DateTimeInterface $createdAt,
        public int $technicalEntriesCount = 0,
        public int $governanceEntriesCount = 0,
    ) {}

    public function totalEntriesCount(): int
    {
        return $this->technicalEntriesCount + $this->governanceEntriesCount;
    }

    /**
     * Returns 'technical' or 'governance' depending on which has more entries.
     * If tied, 'technical' is preferred. Returns null when there are no entries.
     */
    public function dominantDomain(): ?string
    {
        $total = $this->totalEntriesCount();
        if ($total === 0) {
            return null;
        }

        if ($this->technicalEntriesCount >= $this->governanceEntriesCount) {
            return 'technical';
        }

        return 'governance';
    }

    /**
     * Returns the dominant domain percentage (0-100), rounded to the nearest integer.
     */
    public function dominantDomainPercentage(): int
    {
        $total = $this->totalEntriesCount();
        if ($total === 0) {
            return 0;
        }

        $max = max($this->technicalEntriesCount, $this->governanceEntriesCount);

        return (int) round(($max / $total) * 100);
    }

    /**
     * Helper to render strings like "63% technical" or "92% governance".
     * Returns null when there are no entries.
     */
    public function dominantDomainDisplay(): ?string
    {
        $domain = $this->dominantDomain();

        if ($domain === null) {
            return null;
        }

        return sprintf('%d%% %s', $this->dominantDomainPercentage(), $domain);
    }
}

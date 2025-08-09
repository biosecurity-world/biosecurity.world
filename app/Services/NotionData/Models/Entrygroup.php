<?php

namespace App\Services\NotionData\Models;

class Entrygroup
{
    public function __construct(
        public int $id,
        /** @var int[] */
        public array $entries,
    ) {}

    /**
     * Count technical and governance entries within this group.
     *
     * @param  array  $lookup  Map of id => model, containing Entry instances for $this->entries
     * @return array{tech:int,gov:int}
     */
    public function countDomains(array $lookup): array
    {
        $tech = 0;
        $gov = 0;

        foreach ($this->entries as $entryId) {
            $page = $lookup[$entryId] ?? null;
            if (! $page instanceof Entry) {
                continue;
            }

            $mask = $page->getDomainBitmask();
            if (($mask & 1) !== 0) {
                $tech++;
            }
            if (($mask & 2) !== 0) {
                $gov++;
            }
        }

        return ['tech' => $tech, 'gov' => $gov];
    }

    /**
     * Number of entries in Technical domain.
     */
    public function countTechnical(array $lookup): int
    {
        return $this->countDomains($lookup)['tech'];
    }

    /**
     * Number of entries in Governance domain.
     */
    public function countGovernance(array $lookup): int
    {
        return $this->countDomains($lookup)['gov'];
    }
}

<?php

namespace App\View\Components;

use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\Models\Entrygroup as EntrygroupData;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Entrygroup extends Component
{
    /** @var array<string, Collection<int, Entry>> */
    public array $entries;

    /** @param Entry[]  $entries */
    public function __construct(public EntrygroupData $entrygroup, array $entries)
    {
        /** @phpstan-ignore-next-line */
        $this->entries = collect($entries)
            ->groupBy('organizationType')
            ->map(fn (Collection $entries) => $entries->sortByDesc(fn (Entry $entry) => $entry->uniqueness))
            ->sortByDesc(function (Collection $entries) {
                /** @phpstan-ignore-next-line  */
                return $entries->first()->organizationTypeUniqueness;
            })
            ->mapWithKeys(fn (Collection $entries, string $organizationType) => [
                match ($organizationType) {
                    'Research institute / lab / network' => 'Research institute',
                    'International non-profit organization' => 'International NGO',
                    'National non-profit organization' => 'National NGO',
                    default => $organizationType,
                } => $entries,
            ])
            ->toArray();
    }

    public function render(): View|Closure|string
    {
        return view('components.entrygroup');
    }
}

<?php

namespace App\View\Components;

use App\Services\NotionData\DataObjects\Entry;
use App\Services\NotionData\DataObjects\Entrygroup as EntrygroupData;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Entrygroup extends Component
{
    public array $entries;

    /**
     * @param  Entrygroup  $entrygroup
     * @param  Entry[]  $entries
     */
    public function __construct(public EntrygroupData $entrygroup, array $entries)
    {
        $this->entries = collect($entries)
            ->groupBy('organizationType')
            ->map(fn (Collection $entries) => $entries->sortByDesc(fn (Entry $entry) => $entry->uniqueness))
            ->sortByDesc(fn (Collection $entries) => $entries->first()->organizationTypeUniqueness)
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

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.entrygroup');
    }
}

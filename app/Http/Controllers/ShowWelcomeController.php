<?php

namespace App\Http\Controllers;

use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\Models\Entrygroup;
use App\Services\NotionData\Models\InterventionFocus;
use App\Services\NotionData\Models\LocationHint;
use App\Services\NotionData\NotionClient;
use App\Services\NotionData\Tree\Node;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class ShowWelcomeController
{
    public function __invoke(NotionClient $notion): View
    {
        $tree = $notion->tree();

        $nodes = collect($tree->nodes)->map(function (Node $node) use ($tree) {
            $nodeData = $tree->lookup[$node->id];
            $exportedNode = [
                'id' => $node->id,
                'od' => $node->od,
                'depth' => $node->depth,
                'parent' => $node->parentId,
                'trail' => $node->trail,
            ];

            if ($nodeData instanceof Entrygroup) {
                $exportedNode['entries'] = $nodeData->entries;
            }

            return $exportedNode;
        });

        return view('welcome', [
            'tree' => $tree,
            'categorizedFocuses' => $tree->interventionFocuses()
                ->groupBy(fn (InterventionFocus $focus) => $focus->category()->value)
                ->sortKeys()
                ->map->sortBy('label'),
            'filterData' => $tree->entries()->mapWithKeys(function (Entry $entry) {
                return [$entry->id => [
                    $entry->getActivitiesBitmask(),
                    $entry->getFocusesBitmask(),
                    $entry->getDomainBitmask(),
                    $entry->focusesOnGCBRs ? 1 : 0,
                    $entry->getLocationHintsBitmask(),
                ]];
            }),
            'topLevelLocations' => $tree->locationHints()->filter(fn (LocationHint $l) => $l->isTopLevel())->sortBy('label'),
            'categorizedLocations' => $tree->locationHints()
                ->filter(fn (LocationHint $location) => ! $location->isTopLevel() && $location->region() !== null)
                ->groupBy(function (LocationHint $location): string {
                    $region = $location->region();

                    if ($region === null) {
                        throw new \LogicException('Only mapped locations can be grouped by region.');
                    }

                    return $region->value;
                })
                ->sortKeys()
                ->map(fn ($locs) => $locs->sortBy(function (LocationHint $l) {
                    if ($l->isRegionHeader()) {
                        return ['', 0, ''];
                    }
                    $parent = $l->parentCountryLabel();
                    if ($l->isCountry()) {
                        return [$l->label, 0, ''];
                    }
                    if ($parent !== null) {
                        return [$parent, 1, $l->label];
                    }

                    return ['zzz', 0, $l->label];
                })->values()),
            'databaseUrl' => $notion->databaseUrl(),
            'lastEditedAt' => Carbon::instance($notion->lastEditedAt()),
            'nodes' => $nodes,
        ]);
    }
}

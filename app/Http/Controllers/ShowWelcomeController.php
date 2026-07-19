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
use Illuminate\Support\Collection;

class ShowWelcomeController
{
    /**
     * Characters' worth of padding, border and gap that every pill costs on top
     * of its label, used when weighting how wide a column of pills should be.
     */
    private const PILL_BASE_WIDTH = 6;

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

        /** @var \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, InterventionFocus>> $categorizedFocuses */
        $categorizedFocuses = $tree->interventionFocuses()
            ->groupBy(fn (InterventionFocus $focus) => $focus->category()->value)
            ->sortKeys()
            ->map->sortBy('label');

        return view('welcome', [
            'tree' => $tree,
            'categorizedFocuses' => $categorizedFocuses,
            // Width share for each intervention-focus column, proportional to the
            // text it holds, so a short category (Detection) stops reserving a full
            // third of the row. `fr` keeps min-content as its floor, so category
            // headings never get crushed.
            'focusColumns' => $categorizedFocuses
                ->map(fn (Collection $focuses): string => $focuses->sum(
                    fn (InterventionFocus $focus): int => mb_strlen($focus->label) + self::PILL_BASE_WIDTH
                ).'fr')
                ->implode(' '),
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
            // When the static site was last built & deployed. On the GitHub Pages
            // deploy, the welcome page is rendered by `bare export` during the run,
            // so now() equals the deployment time and gets frozen into the static
            // HTML. An optional DEPLOYED_AT env var can override it with an explicit
            // timestamp. Locally it simply shows the current time.
            'deployedAt' => is_string($deployedAt = config('deploy.deployed_at')) ? Carbon::parse($deployedAt) : Carbon::now(),
            'nodes' => $nodes,
        ]);
    }
}

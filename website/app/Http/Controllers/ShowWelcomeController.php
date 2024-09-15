<?php

namespace App\Http\Controllers;

use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\Models\Entrygroup;
use App\Services\NotionData\NotionClient;
use App\Services\NotionData\Tree\Node;
use App\Services\NotionData\Tree\Tree;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class ShowWelcomeController
{
    public function __invoke(NotionClient $notion): View
    {
        $tree = Tree::buildFromPages($notion->pages());

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
            'filterData' => $tree->entries()->mapWithKeys(function (Entry $entry) {
                return [$entry->id => [
                    $entry->getActivitiesBitmask(),
                    $entry->getDomainBitmask(),
                    $entry->focusesOnGCBRs,
                ]];
            }),
            'databaseUrl' => $notion->databaseUrl(),
            'lastEditedAt' => Carbon::instance($notion->lastEditedAt()),
            'nodes' => $nodes,
        ]);
    }
}

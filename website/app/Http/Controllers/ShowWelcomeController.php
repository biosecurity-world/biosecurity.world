<?php

namespace App\Http\Controllers;

use App\Services\NotionData\DataObjects\Entry;
use App\Services\NotionData\DataObjects\Entrygroup;
use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Node;
use App\Services\NotionData\Tree\Tree;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class ShowWelcomeController
{
    public function __invoke(Notion $notion): View
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
            'databaseUrl' => $notion->databaseUrl(),
            'lastEditedAt' => Carbon::instance($notion->lastEditedAt()),
            'nodes' => $nodes,
            'bitmaskLength' => Entry::bitmaskLength(),
            'andOrMask' => Entry::andOrBitmask(),
            'entries' => $tree->entries()->mapWithKeys(fn (Entry $entry) => [$entry->id => $entry->getFilterBitmask()]),
        ]);
    }
}

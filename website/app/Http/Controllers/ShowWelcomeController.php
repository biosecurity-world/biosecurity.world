<?php

namespace App\Http\Controllers;

use App\Services\NotionData\DataObjects\Activity;
use App\Services\NotionData\DataObjects\Entry;
use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;
use Carbon\Carbon;

class ShowWelcomeController
{
    public function __invoke(Notion $notion)
    {
        $tree = Tree::buildFromPages($notion->pages());

        return view('welcome', [
            'tree' => $tree,
            'lookup' => [
                'entries' => $tree->entries()->map(fn (Entry $entry) => [
                    'id' => $entry->id,
                    'activities' => $entry->getActivityBitmask(Activity::$seen),
                    'lenses' => $entry->lens(),
                ]),
                'entrygroups' => $tree->entrygroups(),
            ],
            'databaseUrl' => $notion->databaseUrl(),
            'lastEditedAt' => Carbon::instance($notion->lastEditedAt()),
        ]);
    }
}

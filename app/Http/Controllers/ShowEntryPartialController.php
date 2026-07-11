<?php

namespace App\Http\Controllers;

use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\Models\Entrygroup;
use App\Services\NotionData\NotionClient;
use Illuminate\Contracts\View\View;

class ShowEntryPartialController
{
    public function __invoke(NotionClient $notion, int $entryGroup, int $entryId): View
    {
        $tree = $notion->tree();

        $group = $tree->lookup[$entryGroup] ?? null;
        $entry = $tree->lookup[$entryId] ?? null;

        abort_unless(
            $group instanceof Entrygroup
            && $entry instanceof Entry
            && in_array($entryId, $group->entries, true),
            404
        );

        return view('partials.entry', [
            'entrygroup' => $group,
            'entry' => $entry,
            'breadcrumbs' => collect($tree->nodes)->where('id', $entryGroup)->sole()->breadcrumbs($tree),
        ]);
    }
}

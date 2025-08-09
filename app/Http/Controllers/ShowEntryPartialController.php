<?php

namespace App\Http\Controllers;

use App\Services\NotionData\NotionClient;
use App\Services\NotionData\Tree\Tree;
use Illuminate\Contracts\View\View;

class ShowEntryPartialController
{
    public function __invoke(NotionClient $notion, int $entryGroup, int $entryId): View
    {
        $tree = Tree::buildFromPages($notion->pages());

        abort_if(! isset($tree->lookup[$entryGroup]) || ! isset($tree->lookup[$entryId]), 404);

        return view('partials.entry', [
            'entrygroup' => $tree->lookup[$entryGroup],
            'entry' => $tree->lookup[$entryId],
            'breadcrumbs' => collect($tree->nodes)->where('id', $entryGroup)->sole()->breadcrumbs($tree),
        ]);
    }
}

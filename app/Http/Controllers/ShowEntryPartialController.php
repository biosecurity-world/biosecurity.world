<?php

namespace App\Http\Controllers;

use App\Services\NotionData\NotionClient;
use App\Services\NotionData\Tree\Tree;
use Illuminate\Contracts\View\View;

class ShowEntryPartialController
{
    public function __invoke(NotionClient $notion, int $id, int $entryId): View
    {
        $tree = Tree::buildFromPages($notion->pages());

        abort_if(! isset($tree->lookup[$id]) || ! isset($tree->lookup[$entryId]), 404);

        return view('partials.entry', [
            'entrygroup' => $tree->lookup[$id],
            'entry' => $tree->lookup[$entryId],
            'breadcrumbs' => collect($tree->nodes)->where('id', $id)->sole()->breadcrumbs($tree),
        ]);
    }
}

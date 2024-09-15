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

            // This is used only in development to conditionally load
            // the CSS before the entry's HTML to debug it directly on the page
            // instead of through the map's interface.
            'isXHR' => request()->header('X-Requested-With') === 'XMLHttpRequest',
        ]);
    }
}

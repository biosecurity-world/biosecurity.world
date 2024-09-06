<?php

namespace App\Http\Controllers;

use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;

class ShowEntryController
{
    public function __invoke(Notion $notion, int $id, int $entryId)
    {
        $tree = Tree::buildFromPages($notion->pages());

        abort_if(! isset($tree->lookup[$id]) || ! isset($tree->lookup[$entryId]), 404);

        return view('entries.show', [
            // This is used only in development to conditionally load
            // the CSS before the entry's HTML to debug it directly on the page
            // instead of through the map's interface.
            'isXHR' => request()->header('X-Requested-With') === 'XMLHttpRequest',
            'entrygroup' => $tree->lookup[$id],
            'entry' => $tree->lookup[$entryId],
            'breadcrumbs' => $tree->getNodeById($id)->breadcrumbs($tree),
        ]);
    }
}

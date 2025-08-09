<?php

namespace App\Http\Controllers;

use App\Services\NotionData\NotionClient;

use App\Services\NotionData\Tree\Tree;

class ShowEntryController
{
    public function __invoke(NotionClient $notion, int $id, string $slug)
    {
        $tree = Tree::buildFromPages($notion->pages());

        abort_if(! isset($tree->lookup[$id]), 404);

        $entry = $tree->lookup[$id];

        if ($entry->slug() !== $slug) {
            return redirect()->route('entries.show', ['id' => $id, 'slug' => $entry->slug()]);
        }

        return view('entries.seo', [
            'entry' => $entry,
        ]);
    }
}

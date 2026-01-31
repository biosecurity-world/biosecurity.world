<?php

namespace App\Http\Controllers;

use App\Services\NotionData\NotionClient;

class ShowEntryController
{
    public function __invoke(NotionClient $notion, int $id, string $slug)
    {
        $tree = $notion->tree();

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

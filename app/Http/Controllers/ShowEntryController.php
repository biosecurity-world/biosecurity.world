<?php

namespace App\Http\Controllers;

use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\NotionClient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ShowEntryController
{
    public function __invoke(NotionClient $notion, int $id, string $slug): View|RedirectResponse
    {
        $tree = $notion->tree();

        abort_unless(($tree->lookup[$id] ?? null) instanceof Entry, 404);

        /** @var Entry $entry */
        $entry = $tree->lookup[$id];

        if ($entry->slug() !== $slug) {
            return redirect()->route('entries.show', ['id' => $id, 'slug' => $entry->slug()]);
        }

        return view('entries.seo', [
            'entry' => $entry,
        ]);
    }
}

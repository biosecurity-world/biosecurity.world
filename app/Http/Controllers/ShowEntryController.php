<?php

namespace App\Http\Controllers;

use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\NotionClient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;

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
            'jsonLd' => $this->jsonLd($entry),
        ]);
    }

    /**
     * schema.org Organization describing the entry itself.
     *
     * `url` is the organization's own website, not this page: the page is
     * referenced by mainEntityOfPage, which is what schema.org expects when the
     * described thing lives elsewhere.
     *
     * @return array<string, string>
     */
    private function jsonLd(Entry $entry): array
    {
        $description = trim((string) preg_replace('/\s+/', ' ', $entry->description->toString()));

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $entry->label,
            'url' => $entry->link,
            'logo' => $entry->logo->url,
            'mainEntityOfPage' => rtrim(Config::string('seo.url'), '/')
                .route('entries.show', ['id' => $entry->id, 'slug' => $entry->slug()], absolute: false)
                .'/',
        ];

        if ($description !== '') {
            $data['description'] = $description;
        }

        return $data;
    }
}

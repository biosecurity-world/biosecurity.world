<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\NotionData\Models\Category;
use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\NotionClient;
use Illuminate\Console\Command;

/**
 * Export the published Notion entries as a flat CSV.
 *
 * The output is written to public/data/entries.csv so it ends up:
 *  - bundled into the static site (downloadable at /data/entries.csv), and
 *  - committed back to the repo by the deploy workflow (so the repo on
 *    GitHub also exposes the latest snapshot).
 *
 * Multi-valued fields (activities, intervention focuses, location hints,
 * domains, category trail) are flattened into a single cell with values
 * separated by " ; " for easy use in Excel / pandas while keeping a
 * single-row-per-entry shape.
 */
class ExportData extends Command
{
    protected $signature = 'app:export-data {path?}';

    protected $description = 'Export the published Notion entries as a flat CSV';

    private const SEPARATOR = ' ; ';

    public function handle(NotionClient $notion): int
    {
        $tree = $notion->tree();

        /** @var string $path */
        $path = $this->argument('path') ?? public_path('data/entries.csv');

        $dir = dirname($path);
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            $this->error("Could not create directory: $dir");

            return self::FAILURE;
        }

        $handle = fopen($path, 'w');
        if ($handle === false) {
            $this->error("Could not open $path for writing.");

            return self::FAILURE;
        }

        // Header row
        fputcsv($handle, [
            'id',
            'label',
            'link',
            'organization_type',
            'domains',
            'activities',
            'intervention_focuses',
            'location_hints',
            'focuses_on_gcbrs',
            'category_trail',
            'description',
            'created_at',
            'notion_url',
        ]);

        // Build a lookup of categories by id so we can walk parent chains
        /** @var array<int, Category> $categoriesById */
        $categoriesById = [];
        foreach ($tree->lookup as $id => $page) {
            if ($page instanceof Category) {
                $categoriesById[$id] = $page;
            }
        }

        $entries = $tree->entries()->sortBy(fn (Entry $e) => strtolower($e->label));
        $count = 0;

        foreach ($entries as $entry) {
            fputcsv($handle, [
                (string) $entry->id,
                $entry->label,
                $entry->link,
                $entry->organizationType,
                $entry->domains->map(fn ($d) => $d->name)->implode(self::SEPARATOR),
                $entry->activities->map(fn ($a) => $a->label)->implode(self::SEPARATOR),
                $entry->interventionFocuses->map(fn ($f) => $f->label)->implode(self::SEPARATOR),
                $entry->locationHints->map(fn ($l) => $l->label)->implode(self::SEPARATOR),
                $entry->focusesOnGCBRs ? 'yes' : 'no',
                $this->categoryTrail($entry, $categoriesById),
                $this->cleanDescription($entry->description->toString()),
                $entry->createdAt->format(\DateTimeInterface::ATOM),
                $entry->notionUrl(),
            ]);
            $count++;
        }

        fclose($handle);

        $this->info("Exported $count entries to $path");

        return self::SUCCESS;
    }

    /**
     * Walk up the category chain from the entry's parent and join labels with " > ".
     *
     * @param  array<int, Category>  $categoriesById
     */
    private function categoryTrail(Entry $entry, array $categoriesById): string
    {
        $trail = [];
        $currentId = $entry->parentId;

        // Guard against accidental cycles in malformed data
        $seen = [];
        while ($currentId !== null && isset($categoriesById[$currentId]) && ! isset($seen[$currentId])) {
            $seen[$currentId] = true;
            $cat = $categoriesById[$currentId];
            array_unshift($trail, $cat->label);
            $currentId = $cat->parentId;
        }

        return implode(' > ', $trail);
    }

    /**
     * Collapse whitespace in the description so the CSV stays one row per entry.
     */
    private function cleanDescription(string $text): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $text));
    }
}

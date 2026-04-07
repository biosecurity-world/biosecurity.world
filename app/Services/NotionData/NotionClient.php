<?php

declare(strict_types=1);

namespace App\Services\NotionData;

use App\Services\NotionData\Models\Activity;
use App\Services\NotionData\Models\InterventionFocus;
use App\Services\NotionData\Models\LocationHint;
use App\Services\NotionData\Tree\Tree;
use App\Support\IdMap;
use Notion\Databases\Database;
use Notion\Notion as NotionWrapper;
use Notion\Pages\Page;

class NotionClient
{
    protected NotionWrapper $client;

    private string $databaseId;

    public function __construct(string $databaseId, string $notionToken)
    {
        $this->databaseId = str_replace('-', '', $databaseId);
        $this->client = NotionWrapper::create($notionToken);
    }

    public function lastEditedAt(): \DateTimeInterface
    {
        return $this->database()->lastEditedTime;
    }

    protected function database(): Database
    {
        return cache()->rememberForever('database', fn () => $this->client->databases()->find($this->databaseId));
    }

    public function pages(): HydratedPages
    {
        $database = $this->database();

        $pages = cache()->rememberForever('pages', fn () => $this->client->databases()->queryAllPages($database));

        return (new Hydrator($database))->hydrate(
            array_filter($pages, function (Page $page) {
                if ($page->archived) {
                    return false;
                }

                try {
                    $isCategory = $page->properties()->getCheckboxById(Hydrator::SCHEMA['isCategory'])->checked;
                    if ($isCategory) {
                        return true;
                    }

                    $status = $page->properties()->getStatus('Status');

                    return $status->option?->name !== 'Pending';
                } catch (\Throwable) {
                    return true;
                }
            })
        );
    }

    /**
     * Get the built tree, cached to avoid rebuilding on every request.
     * Also caches and restores static state needed for ID lookups and bitmask calculations.
     */
    public function tree(): Tree
    {
        $cached = cache()->get('tree_with_state');

        if ($cached !== null) {
            // Restore all static state from cache
            IdMap::$idMap = $cached['idMap'];
            Activity::restoreState($cached['activityState']);
            InterventionFocus::restoreState($cached['focusState']);
            LocationHint::restoreState($cached['locationState']);

            return $cached['tree'];
        }

        // Build tree (this populates static state during hydration)
        $tree = Tree::buildFromPages($this->pages());

        // Cache tree along with all static state
        cache()->forever('tree_with_state', [
            'tree' => $tree,
            'idMap' => IdMap::$idMap,
            'activityState' => Activity::getState(),
            'focusState' => InterventionFocus::getState(),
            'locationState' => LocationHint::getState(),
        ]);

        return $tree;
    }

    public function databaseUrl(): string
    {
        return 'https://notion.so/'.$this->databaseId;
    }
}

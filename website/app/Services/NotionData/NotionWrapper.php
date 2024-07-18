<?php

namespace App\Services\NotionData;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Notion\Notion;
use Notion\Pages\Page;
use App\Services\NotionData\Models\Category;
use App\Services\NotionData\Models\Entry;


class NotionWrapper
{
    protected Notion $client;
    private string $databaseId;

    public function __construct(string $databaseId, string $notionToken)
    {
        $this->databaseId = str_replace('-', '', $databaseId);
        $this->client = Notion::create($notionToken);
    }


    /** @return Collection<string, Category|Entry> */
    public function pages(): Collection {
        $database = Cache::rememberForever(
            'database',
            fn() => $this->client->databases()->find($this->databaseId),
        );

        $pages = Cache::rememberForever(
            'pages',
            fn () => $this->client->databases()->queryAllPages($database),
        );

        return collect($pages)
            ->filter(fn (Page $page) => !$page->archived)
            ->mapWithKeys(fn (Page $page) => [$page->id => Hydrator::hydrate($page, $database)]);
    }

    public function databaseUrl()
    {
        return "https://notion.so/" . $this->databaseId;
    }
}

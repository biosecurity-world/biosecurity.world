<?php

declare(strict_types=1);

namespace App\Services\NotionData;

use Illuminate\Support\Facades\Cache;
use Notion\Databases\Database;
use Notion\Notion as NotionWrapper;
use Notion\Pages\Page;

class Notion
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
        /** @phpstan-ignore-next-line */
        return Cache::rememberForever('database', fn () => $this->client->databases()->find($this->databaseId));
    }

    public function pages(): HydratedPages
    {
        $database = $this->database();

        /** @var Page[] $pages */
        $pages = Cache::rememberForever('pages', fn () => $this->client->databases()->queryAllPages($database));

        // sort pages by last edited time
        usort($pages, fn (Page $a, Page $b) => $a->lastEditedTime <=> $b->lastEditedTime);

        return (new Hydrator($database))->hydrate(
            array_filter($pages, fn (Page $page) => ! $page->archived)
        );
    }

    public function databaseUrl(): string
    {
        return 'https://notion.so/'.$this->databaseId;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\NotionData;

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
        return cache()->rememberForever('database', fn () => $this->client->databases()->find($this->databaseId));
    }

    public function pages(): HydratedPages
    {
        $database = $this->database();

        $pages = cache()->rememberForever('pages', fn () => $this->client->databases()->queryAllPages($database));

        return (new Hydrator($database))->hydrate(
            array_filter($pages, fn (Page $page) => ! $page->archived)
        );
    }

    public function databaseUrl(): string
    {
        return 'https://notion.so/'.$this->databaseId;
    }
}

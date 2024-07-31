<?php

namespace App\Services\NotionData;

use App\Services\NotionData\Enums\Color;
use App\Services\NotionData\Enums\PageType;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Notion\Databases\Database;
use Notion\Notion as NotionWrapper;
use Notion\Pages\Page as NotionPage;


class Notion
{
    protected NotionWrapper $client;
    private string $databaseId;

    public function __construct(string $databaseId, string $notionToken)
    {
        $this->databaseId = str_replace('-', '', $databaseId);
        $this->client = NotionWrapper::create($notionToken);
    }

    /** @return Collection<string, Page> */
    public function pages(): Collection
    {
        $database = $this->database();
        $pages = Cache::rememberForever(
            'pages',
            fn() => $this->client->databases()->queryAllPages($database),
        );

        return collect($pages)
            ->filter(fn(NotionPage $page) => !$page->archived)
            ->mapWithKeys(fn(NotionPage $page) => [$page->id => Hydrator::hydrate($page, $database)]);
    }

    protected function database(): Database
    {
        return Cache::rememberForever(
            'database',
            function () {
                return $this->client->databases()->find($this->databaseId);
            },
        );
    }

    protected function getMultiSelectOptions(string $localSchemaName): array
    {
        $database = $this->database();
        $options = collect(
            $database->properties()->getMultiSelectById(
                Schema::schemaForDatabase($database->id)[$localSchemaName]
            )->options
        )
            ->mapWithKeys(fn ($option) => [$option->id => array_merge(
                $option->toArray(),
                ['count' => 0]
            )])->toArray();


        $pages = $this->pages();
        foreach ($pages as $page) {
            if ($page->type !== PageType::Entry) {
                continue;
            }

            foreach ($page->data[$localSchemaName] as $opt) {
                $options[$opt]['count']++;
            }
        }

        return $options;
    }

    public function lastEditedAt(): Carbon {
        return Carbon::create($this->database()->lastEditedTime);
    }

    public function activityTypes(): array
    {
        $options =  $this->getMultiSelectOptions("activityTypes");

        foreach ($options as $k => $option) {
            $options[$k]['icon'] = match ($option['name']) {
                "Coordination / strategy" => "strategy",
                "Lobbying" => "lobbying",
                "Funding / philanthropy" => "funding",
                "Research" => "research",
                "Technology development" => "technology",
                "Policy development / consultancy" => "policy",
                "Public advocacy / campaigning / outreach" => "advocacy",
                "Education / career support" => "education",
                default => null,
            };

            $color = Color::tryFrom($option['color']);
            if (!$color) {
                report(new \Exception("Unexpected color from Notion: " . $option['color']));
                $color = Color::Default;
            }

            $options[$k]['fg'] = $color->foreground();
            $options[$k]['bg'] = $color->background();
        }

        return  $options;
    }

    public function interventionFocuses(): array
    {
        return $this->getMultiSelectOptions("interventionFocuses");
    }


    public function databaseUrl(): string
    {
        return "https://notion.so/" . $this->databaseId;
    }
}

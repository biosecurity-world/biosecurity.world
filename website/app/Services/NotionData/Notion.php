<?php

namespace App\Services\NotionData;

use App\Services\NotionData\Enums\NotionColor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Notion\Common\Color;
use Notion\Databases\Database;
use Notion\Databases\Properties\SelectOption;
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

    /** @return array<Entry|Category> */
    public function pages(): array
    {
        return Cache::rememberForever('hydrated-pages', function () {
            $database = $this->database();

            /** @var Page[] $pages */
            $pages = Cache::rememberForever('pages', fn () => $this->client->databases()->queryAllPages($database));

            return (new Hydrator($database))->hydrate(
                array_filter($pages, fn (Page $page) => ! $page->archived)
            );
        });
    }

    protected function database(): Database
    {
        /** @phpstan-ignore-next-line */
        return Cache::rememberForever('database', fn () => $this->client->databases()->find($this->databaseId));
    }

    /** @return array{id: ?string, color: ?Color, name: ?string, description: ?string, count: int}[] */
    protected function getMultiSelectOptions(string $localPropertyName): array
    {
        $options = collect($this->pages())
            ->filter(fn (Entry|Category $page) => $page instanceof Entry)
            ->flatMap(fn (Entry $page) => $page->{$localPropertyName})
            ->groupBy(fn (SelectOption $opt) => $opt->id)
            ->map(fn ($group) => array_merge($group->first()->toArray(), ['count' => count($group)]))
            ->values()
            ->toArray();

        /** @phpstan-ignore-next-line */
        return $options;
    }

    public function lastEditedAt(): Carbon
    {
        return Carbon::instance($this->database()->lastEditedTime);
    }

    public function getOrganizationTypeNoun(string $organizationType): string
    {
        return match ($organizationType) {
            'For-profit company' => 'company',
            'Think tank' => 'think tank',
            'Government' => 'governmental organization',
            'Intergovernmental agency' => 'intergovernmental agency',
            'National non-profit organization' => 'national NGO',
            'International non-profit organization' => 'international NGO',
            'Media' => 'media organization',
            'Research institute / lab / network' => 'research institute',
            default => 'organization',
        };
    }

    /** @return array[] */
    public function activityTypes(): array
    {
        $options = $this->getMultiSelectOptions('activityTypes');

        foreach ($options as $k => $option) {
            $options[$k]['icon'] = match ($option['name']) {
                'Coordination / strategy' => 'strategy',
                'Lobbying' => 'lobbying',
                'Funding / philanthropy' => 'funding',
                'Research' => 'research',
                'Technology development' => 'technology',
                'Policy development / consultancy' => 'policy',
                'Public advocacy / campaigning / outreach' => 'advocacy',
                'Education / career support' => 'education',
                default => null,
            };

            $color = NotionColor::tryFrom($option['color']);
            if (! $color) {
                report(new \Exception('Unexpected color from Notion: '.$option['color']));
                $color = NotionColor::Default;
            }

            $options[$k]['fg'] = $color->foreground();
            $options[$k]['bg'] = $color->background();
        }

        return $options;
    }

    /** @return array[] */
    public function interventionFocuses(): array
    {
        return $this->getMultiSelectOptions('interventionFocuses');
    }

    public function databaseUrl(): string
    {
        return 'https://notion.so/'.$this->databaseId;
    }
}

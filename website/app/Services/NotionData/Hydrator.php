<?php

declare(strict_types=1);

namespace App\Services\NotionData;

use App\Services\Iconsnatch\IconSnatch;
use App\Services\NotionData\DataObjects\Activity;
use App\Services\NotionData\DataObjects\Category;
use App\Services\NotionData\DataObjects\Entry;
use App\Services\NotionData\DataObjects\InterventionFocus;
use App\Services\NotionData\DataObjects\Location;
use App\Services\NotionData\Enums\NotionColor;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Notion\Databases\Database;
use Notion\Databases\Properties\SelectOption;
use Notion\Pages\Page;

class Hydrator
{
    /*
     * The IDs are hardcoded (no other way is much better) but they do not change,
     * even if the database is duplicated or the column changes, so they are _very_ stable.
     */
    public const array SCHEMA = [
        'organizationType' => '%3EfkD',
        'link' => 'BEe%7D',
        'description' => 'C%3Fc%3A',
        'interventionFocuses' => 'L%3FRx',
        'parent' => 'QTQ%5D',
        'locationHints' => 'VQ%5B%7D',
        'activityTypes' => 'Wmi~',
        'gcbrFocus' => 'kC%5Cr',
        'name' => 'title',
        'isCategory' => 'uR%3DA',
    ];

    public function __construct(protected Database $database) {}

    /**
     * @param  Page[]  $pages
     * @return array<Category|Entry>
     */
    public function hydrate(array $pages): array
    {
        $hydrated = [];

        // These are used to calculate the uniqueness properties in Entry.
        $entryCountMap = [];
        $organizationTypeMap = [];

        foreach ($pages as $page) {
            $parents = $page->properties()->getRelationById(self::SCHEMA['parent'])->pageIds;
            $isCategory = $page->properties()->getCheckboxById(self::SCHEMA['isCategory'])->checked;

            if (count($parents) === 0) {
                if (! $isCategory) {
                    continue;
                }

                $category = $this->categoryFromPage($page, null);
                if (is_null($category)) {
                    continue;
                }
                $hydrated[] = $category;

                continue;
            }

            $item = $isCategory ?
                $this->categoryFromPage($page, '') :
                $this->entryFromPage($page, '');

            if (is_null($item)) {
                continue;
            }

            foreach ($parents as $parent) {
                $clone = clone $item;
                $clone->parentId = $parent;
                $hydrated[] = $clone;

                if ($item instanceof Entry) {
                    if (!isset($entryCountMap[$parent])) {
                        $entryCountMap[$parent] = 0;
                    }

                    if (!isset($organizationTypeMap[$item->organizationType])) {
                        $organizationTypeMap[$item->organizationType] = 0;
                    }

                    $entryCountMap[$parent]++;
                    $organizationTypeMap[$item->organizationType]++;
                }
            }

        }

        foreach ($hydrated as $item) {
            if ($item instanceof Entry) {
                $item->uniqueness = 1 / $entryCountMap[$item->parentId];
                $item->organizationTypeUniqueness = 1 / $organizationTypeMap[$item->organizationType];
            }
        }

        return $hydrated;
    }

    public function categoryFromPage(Page $page, ?string $parentId): ?Category
    {
        $validator = Validator::make(
            ['title' => $page->title()?->toString()],
            ['title' => ['required', 'string']]
        );

        if ($validator->fails()) {
            return null;
        }

        return new Category($page->id, $parentId, $validator->validated()['title']);
    }

    public function entryFromPage(Page $page, string $parentId): ?Entry
    {
        $link = $page->properties()->getUrlById(self::SCHEMA['link'])->url;

        $validator = Validator::make(
            [
                'id' => $page->id,
                'link' => ! str_starts_with($link ?? '', 'http') ? 'https://'.$link : $link,
                'label' => $page->title()?->toString(),
                'description' => $page->properties()->getRichTextById(self::SCHEMA['description']),
                'organizationType' => $page->properties()->getSelectById(self::SCHEMA['organizationType'])->option?->name,
                'activities' => $page->properties()->getMultiSelectById(self::SCHEMA['activityTypes'])->options,
                'interventionFocuses' => $page->properties()->getMultiSelectById(self::SCHEMA['interventionFocuses'])->options,
                'location' => $page->properties()->getMultiSelectById(self::SCHEMA['locationHints'])->options,
                'gcbrFocus' => $page->properties()->getCheckboxById(self::SCHEMA['gcbrFocus'])->checked,
            ],
            [
                'id' => ['required', 'string'],
                'label' => ['required', 'string'],
                'description' => ['required'],
                'gcbrFocus' => ['required', 'boolean'],
                'link' => ['required', 'string', 'url'],
                'organizationType' => ['required', 'string'],
                ...collect(['activities', 'interventionFocuses', 'location'])->mapWithKeys(fn (string $multiSelect) => [$multiSelect => [
                    "$multiSelect.*.id" => ['required', 'string'],
                    "$multiSelect.*.name" => ['required', 'string'],
                    "$multiSelect.*.color" => ['required', 'string', new In(Arr::pluck(NotionColor::cases(), 'value'))],
                ]]),
            ]
        );

        if ($validator->fails()) {
            return null;
        }

        $data = $validator->validated();
        $data['parentId'] = $parentId;
        $data['activities'] = array_map(fn (SelectOption $opt) => Activity::fromNotionOption($opt), $data['activities']);
        $data['interventionFocuses'] = array_map(fn (SelectOption $opt) => InterventionFocus::fromNotionOption($opt), $data['interventionFocuses']);
        $data['location'] = Location::fromNotionOptions($data['location']);
        $data['logo'] = IconSnatch::downloadFrom($data['link']);

        return new Entry(...$data);
    }
}

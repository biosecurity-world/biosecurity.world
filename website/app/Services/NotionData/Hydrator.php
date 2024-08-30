<?php

namespace App\Services\NotionData;

use App\Rules\CachedActiveUrlRule;
use App\Services\Iconsnatch\IconSnatch;
use App\Services\Iconsnatch\Logo;
use Exception;
use Illuminate\Support\Facades\Validator;
use Notion\Databases\Database;
use Notion\Pages\Page;

class Hydrator
{
    /*
     * The IDs are hardcoded (no other way is much better) but they do not change,
     * even if the database is duplicated or the column changes, so they are _very_ stable.
     */
    public const array SCHEMA = [
        "organizationType" => "%3EfkD",
        "link" => "BEe%7D",
        "description" => "C%3Fc%3A",
        "interventionFocuses" => "L%3FRx",
        "parent" => "QTQ%5D",
        "locationHints" => "VQ%5B%7D",
        "activityTypes" => "Wmi~",
        "gcbrFocus" => "kC%5Cr",
        "name" => "title",
        "isCategory" => "uR%3DA",
    ];

    public function __construct(protected Database $database)
    {
    }

    /**
     * @param Page[] $pages
     * @return array<Category|Entry>
     */
    public function hydrate(array $pages): array
    {
        $hydrated = [];

        foreach ($pages as $page) {
            $parents = $page->properties()->getRelationById(self::SCHEMA["parent"])->pageIds;
            $isCategory = $page->properties()->getCheckboxById(self::SCHEMA["isCategory"])->checked;

            if (count($parents) === 0) {
                if (!$isCategory) {
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

        return new Category($page->id, $parentId, $validator->validated()["title"]);
    }

    public function entryFromPage(Page $entry, string $parentId): ?Entry
    {
        $link = $entry->properties()->getUrlById(self::SCHEMA["link"])->url;
        $organizationType = $entry->properties()->getSelectById(self::SCHEMA["organizationType"])->option;

        $validator = Validator::make(
            [
                'link' => !str_starts_with($link ?? '', 'http') ? 'https://' . $link : $link,
                'title' => $entry->title()?->toString(),
                'organizationType' => $organizationType?->name,
            ],
            [
                'link' => ['required', 'string', 'url'],
                'title' => ['required', 'string'],
                'organizationType' => ['required', 'string'],
            ]
        );

        if ($validator->fails()) {
            return null;
        }

        $validData = $validator->validated();

        return new Entry(
            id: $entry->id,
            parentId: $parentId,
            label: $validData["title"],
            link: $validData["link"],
            description: $entry->properties()->getRichTextById(self::SCHEMA["description"])->toString(),
            organizationType: $validData["organizationType"],
            interventionFocuses: $entry->properties()->getMultiSelectById(self::SCHEMA["interventionFocuses"])->options,
            activityTypes: $entry->properties()->getMultiSelectById(self::SCHEMA["activityTypes"])->options,
            locationHints: $entry->properties()->getMultiSelectById(self::SCHEMA["locationHints"])->options,
            gcbrFocus: $entry->properties()->getCheckboxById(self::SCHEMA["gcbrFocus"])->checked,
            logo: IconSnatch::downloadFrom($validData["link"]),
        );
    }

}

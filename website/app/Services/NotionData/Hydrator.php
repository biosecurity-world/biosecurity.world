<?php

namespace App\Services\NotionData;

use App\Services\Iconsnatch\IconSnatch;
use App\Services\NotionData\Enums\NodeType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Notion\Databases\Database;
use Notion\Databases\Properties\SelectOption;
use Notion\Pages\Page as NotionPage;

class Hydrator
{
    // The IDs are hardcoded (no other way is much better) but they do not change,
    // even if the database is duplicated or the column changes, so they are _very_ stable.
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

    public function pageFromRawCategory(NotionPage $page, string $id,?string $parent): Page
    {
        return new Page(NodeType::Category, $id, $parent, [
            "label" => $page->title()->toString(),
        ]);
    }

    public function pageFromCategoryOrEntry(NotionPage $page, string $id, ?string $parent): ?Page
    {
        $isCategory = $page->properties()->getCheckboxById(self::SCHEMA["isCategory"])->checked;
        if ($isCategory) {
            return $this->pageFromRawCategory($page, $id, $parent);
        }

        return $this->pageFromRawEntry($page, $id, $parent);
    }

    public function pageFromRawEntry(NotionPage $entry, string $id, ?string $parent): ?Page
    {
        $link = $entry->properties()->getUrlById(self::SCHEMA["link"])->url;
        if (is_null($link)) {
            return null;
        }

        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            if (!filter_var("https://$link", FILTER_VALIDATE_URL)) {
                return null;
            }

            $link = "https://$link";
        }

        $logo = Cache::rememberForever(
            'iconsnatch-download-' . str_replace(str_split('{}()/\@:'), '', $link),
            // Returning null would prevent Laravel from caching the icon.
            fn() => IconSnatch::downloadFrom($link) ?? false,
        );

        if ($logo === false) {
            $logo = null;
        }

        return new Page(NodeType::Entry, $id, $parent, [
            "label" => $entry->title()->toString(),
            "link" => $link,
            "description" => $entry->properties()->getRichTextById(self::SCHEMA["description"])->toString(),
            "organizationType" => $entry->properties()->getSelectById(self::SCHEMA["organizationType"])->option?->name,
            "interventionFocuses" =>                 $entry->properties()->getMultiSelectById(self::SCHEMA["interventionFocuses"])->options,
            "activityTypes" => $entry->properties()->getMultiSelectById(self::SCHEMA["activityTypes"])->options,
            "locationHints" => $entry->properties()->getMultiSelectById(self::SCHEMA["locationHints"])->options,
            "gcbrFocus" => $entry->properties()->getCheckboxById(self::SCHEMA["gcbrFocus"])->checked,
            "logo" => $logo
        ]);
    }


    public function __construct(protected Database $database)
    {
    }

    public function hydrate(Collection $pages): Collection
    {
        $hydrated = collect();

        /** @var NotionPage $page */
        foreach ($pages as $page) {
            $parents = $page->properties()->getRelationById(self::SCHEMA["parent"])->pageIds;
            if (count($parents) === 0) {
                $parents = [null];
            }

            foreach ($parents as $parent) {
                $hydratedPage = $this->pageFromCategoryOrEntry($page, $page->id, $parent);
                if ($hydratedPage) {
                    $hydrated[] = $hydratedPage;
                }
            }

        }

        return $hydrated;
    }

}

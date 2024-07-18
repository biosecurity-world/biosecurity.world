<?php

namespace App\Services\NotionData;

use Exception;
use Illuminate\Support\Facades\Cache;
use Notion\Databases\Database;
use Notion\Pages\Page;
use App\Services\NotionData\Models\Category;
use App\Services\NotionData\Models\Entry;
use App\Services\NotionData\Support\IconSnatch;

class Hydrator
{
    public const array SCHEMA = [
        "0f5d415db7b4410e9e9bab814c37af8e" => [ // Félix's Test database
            "organizationType" => "%3EfkD",
            "link" => "BEe%7D",
            "description" => "C%3Fc%3A",
            "interventionFocus" => "L%3FRx",
            "parent" => "QTQ%5D",
            "location" => "VQ%5B%7D",
            "activityType" => "Wmi~",
            "gcbrFocus" => "kC%5Cr",
            "name" => "title",
            "isCategory" => "uR%3DA",
        ]
    ];

    public static function hydrate(Page $page, Database $notionDatabase): Category|Entry
    {
        $databaseId = str_replace('-', '', $notionDatabase->id);
        if (!array_key_exists($databaseId, self::SCHEMA)) {
            throw new Exception("Could not find a schema for the database `" . $notionDatabase->id . "`");
        }

        $schema = self::SCHEMA[$databaseId];

        $isCategory = $page->properties()->getCheckboxById($schema["isCategory"])->checked;

        if ($isCategory) {
            return new Category(
                id: $page->id,
                label: $page->title()->toString(),
                parents: $page->properties()->getRelationById($schema["parent"])->pageIds
            );
        }

        return new Entry(
            id: $page->id,
            label: $page->title()->toString(),
            link: $link = $page->properties()->getUrlById($schema["link"])->url,
            description: $page->properties()->getRichTextById($schema["description"])->toString(),
            organizationType: $page->properties()->getSelectById($schema["organizationType"])->option?->name,
            interventionFocuses: $page->properties()->getMultiSelectById($schema["interventionFocus"])->options,
            activityTypes: $page->properties()->getMultiSelectById($schema["activityType"])->options,
            locationHints: $page->properties()->getMultiSelectById($schema["location"])->options,
            gcbrFocus: $page->properties()->getCheckboxById($schema["gcbrFocus"])->checked,
            logo: Cache::rememberForever(
                'iconsnatch-download-' . str_replace(str_split('{}()/\@:'), '', $link),
                fn () => IconSnatch::downloadFrom($link),
            ),
            parents: $page->properties()->getRelationById($schema["parent"])->pageIds
        );
    }
}

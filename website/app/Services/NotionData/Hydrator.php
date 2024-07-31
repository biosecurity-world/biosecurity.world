<?php

namespace App\Services\NotionData;

use App\Services\Iconsnatch\IconSnatch;
use App\Services\NotionData\Enums\PageType;
use Illuminate\Support\Facades\Cache;
use Notion\Databases\Database;
use Notion\Databases\Properties\SelectOption;
use Notion\Pages\Page as NotionPage;

class Hydrator
{
    public static function hydrate(NotionPage $page, Database $notionDatabase): Page
    {
        $schema = Schema::schemaForDatabase($notionDatabase->id);

        $parents = $page->properties()->getRelationById($schema["parent"])->pageIds;
        $isCategory = $page->properties()->getCheckboxById($schema["isCategory"])->checked;

        if ($isCategory) {
            return new Page(PageType::Category, $page->id, $parents, [
                "label" => $page->title()->toString()
            ]);
        }

        $link = $page->properties()->getUrlById($schema["link"])->url;
        $logo = Cache::rememberForever(
            'iconsnatch-download-' . str_replace(str_split('{}()/\@:'), '', $link),
            // Returning null would prevent Laravel from caching the icon.
            fn() => IconSnatch::downloadFrom($link) ?? false,
        );

        if ($logo === false) {
            $logo = null;
        }

        return new Page(PageType::Entry, $page->id, $parents, [
            "label" => $page->title()->toString(),
            "link" => $link,
            "description" => $page->properties()->getRichTextById($schema["description"])->toString(),
            "organizationType" => $page->properties()->getSelectById($schema["organizationType"])->option?->name,
            "interventionFocuses" => array_map(
                fn (SelectOption $option) => $option->id,
                $page->properties()->getMultiSelectById($schema["interventionFocuses"])->options
            ),
            "activityTypes" => array_map(
                fn (SelectOption $option) => $option->id,
                $page->properties()->getMultiSelectById($schema["activityTypes"])->options
            ),
            "locationHints" => array_map(
                fn (SelectOption $option) => $option->id,
                $page->properties()->getMultiSelectById($schema["locationHints"])->options
            ),
            "gcbrFocus" => $page->properties()->getCheckboxById($schema["gcbrFocus"])->checked,
            "logo" => $logo
        ]);
    }
}

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
use App\Support\IdHash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\ValidationException;
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

    /** @param  Page[]  $pages */
    public function hydrate(array $pages): HydratedPages
    {
        $hydrated = [];
        $errors = [];

        // These are used to calculate the uniqueness properties in Entry.
        $entryCountMap = [];
        $organizationTypeMap = [];

        foreach ($pages as $page) {
            $parents = $page->properties()->getRelationById(self::SCHEMA['parent'])->pageIds;
            $isCategory = $page->properties()->getCheckboxById(self::SCHEMA['isCategory'])->checked;

            if (count($parents) === 0) {
                if (! $isCategory) {
                    $errors[] = HydrationError::fromString('Only categories can be top-level items.', $page);

                    continue;
                }

                try {
                    $hydrated[] = $this->categoryFromPage($page, null);
                } catch (ValidationException $e) {
                    $errors[] = HydrationError::fromValidationException($e, $page);
                }

                continue;
            }

            try {
                $item = $isCategory ?
                    $this->categoryFromPage($page, '') :
                    $this->entryFromPage($page, '');
            } catch (ValidationException $e) {
                $errors[] = HydrationError::fromValidationException($e, $page);

                continue;
            }

            foreach ($parents as $parent) {
                $clone = clone $item;
                $clone->parentId = $parent;
                $hydrated[] = $clone;

                if ($item instanceof Entry) {
                    if (! isset($entryCountMap[$parent])) {
                        $entryCountMap[$parent] = 0;
                    }

                    if (! isset($organizationTypeMap[$item->organizationType])) {
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

        return new HydratedPages($hydrated, $errors);
    }

    public function categoryFromPage(Page $page, ?string $parentId): Category
    {
        $validated = Validator::validate(
            ['title' => $page->title()?->toString()],
            ['title' => ['required', 'string']]
        );

        return new Category($page->id, $parentId, $validated['title']);
    }

    public function entryFromPage(Page $page, string $parentId): Entry
    {
        $link = $page->properties()->getUrlById(self::SCHEMA['link'])->url;

        $data = Validator::validate(
            [
                'id' => $page->id,
                'link' => ! str_starts_with($link ?? '', 'http') ? 'https://'.$link : $link,
                'label' => $page->title()?->toString(),
                'description' => $page->properties()->getRichTextById(self::SCHEMA['description']),
                'organizationType' => $page->properties()->getSelectById(self::SCHEMA['organizationType'])->option?->name,
                'activities' => array_map(fn(SelectOption $opt) => Activity::fromNotionOption($opt), $page->properties()->getMultiSelectById(self::SCHEMA['activityTypes'])->options),
                'interventionFocuses' => array_map(fn (SelectOption $opt) => InterventionFocus::fromNotionOption($opt), $page->properties()->getMultiSelectById(self::SCHEMA['interventionFocuses'])->options),
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
                "interventionFocuses" => ['required', 'array', function ($attribute, $value, $fail) {
                    $hasSupertype = collect($value)->contains(fn (InterventionFocus $focus) => $focus->isGovernance() || $focus->isTechnical());
                    if (! $hasSupertype) {
                        $fail('At least one intervention focus must be either [TECHNICAL] or [GOVERNANCE].');
                    }
                }],
                'activities' => ['required', 'array'],
                'location' => ['required', 'array']
            ]
        );

        $data['parentId'] = $parentId;
        $data['location'] = Location::fromNotionOptions($data['location']);
        $data['logo'] = IconSnatch::downloadFrom($data['link']);

        return new Entry(...$data);
    }
}

<?php

declare(strict_types=1);

namespace App\Services\NotionData;

use App\Rules\OkStatusRule;
use App\Services\Logosnatch\Logosnatch;
use App\Services\NotionData\DataObjects\Activity;
use App\Services\NotionData\DataObjects\Category;
use App\Services\NotionData\DataObjects\Entry;
use App\Services\NotionData\DataObjects\InterventionFocus;
use App\Services\NotionData\DataObjects\Location;
use App\Support\IdMap;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Notion\Databases\Database;
use Notion\Databases\Properties\SelectOption;
use Notion\Pages\Page;

class Hydrator
{
    public static bool $strict = false;

    public static function setStrictMode(bool $strict): void
    {
        self::$strict = $strict;
    }

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
        usort($pages, fn (Page $a, Page $b) => $a->createdTime <=> $b->createdTime);

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
                    $errors[] = HydrationError::fromString('Entries must belong to a category.', $page);

                    continue;
                }

                try {
                    $hydrated[] = $this->categoryFromPage($page, null);
                } catch (ValidationException $e) {
                    $errors[] = HydrationError::fromValidationException($e, $page);
                }

                continue;
            }

            if (count($parents) > 1 && $isCategory) {
                $errors[] = HydrationError::fromString('Categories cannot have multiple parents.', $page);

                continue;
            }

            try {
                $item = $isCategory ?
                    $this->categoryFromPage($page, -1) :
                    $this->entryFromPage($page, -1);
            } catch (ValidationException $e) {
                $errors[] = HydrationError::fromValidationException($e, $page);

                continue;
            }

            foreach ($parents as $parent) {
                $clone = clone $item;
                $clone->parentId = IdMap::hash($parent);
                $hydrated[] = $clone;

                if (! ($item instanceof Entry)) {
                    continue;
                }

                $entryCountMap[$clone->parentId] ??= 0;
                $entryCountMap[$clone->parentId]++;

                $organizationTypeMap[$item->organizationType] ??= 0;
                $organizationTypeMap[$item->organizationType]++;
            }
        }

        foreach ($hydrated as $item) {
            if (! ($item instanceof Entry)) {
                continue;
            }

            $item->uniqueness = 1 / $entryCountMap[$item->parentId];
            $item->organizationTypeUniqueness = 1 / $organizationTypeMap[$item->organizationType];
        }

        return new HydratedPages($hydrated, $errors);
    }

    /** @throws ValidationException */
    public function categoryFromPage(Page $page, ?int $parentId): Category
    {
        $validated = Validator::make(
            ['title' => $page->title()?->toString()],
            ['title' => ['required', 'string']]
        )->validate();

        return new Category(IdMap::hash($page->id), $parentId, $validated['title'], $page->createdTime);
    }

    /** @throws ValidationException */
    public function entryFromPage(Page $page, int $parentId): Entry
    {
        $props = $page->properties();

        $link = $props->getUrlById(self::SCHEMA['link'])->url;

        $rawPage = [
            'id' => IdMap::hash($page->id),
            'link' => ! str_starts_with($link ?? '', 'http') ? 'https://'.$link : $link,
            'raw_link' => $link,
            'label' => $page->title()?->toString(),
            'description' => $props->getRichTextById(self::SCHEMA['description']),
            'organizationType' => $props->getSelectById(self::SCHEMA['organizationType'])->option?->name,
            'activities' => array_map(
                fn (SelectOption $opt) => Activity::fromNotionOption($opt),
                $props->getMultiSelectById(self::SCHEMA['activityTypes'])->options
            ),
            'interventionFocuses' => array_map(
                fn (SelectOption $opt) => InterventionFocus::fromNotionOption($opt),
                $props->getMultiSelectById(self::SCHEMA['interventionFocuses'])->options
            ),
            'location' => $props->getMultiSelectById(self::SCHEMA['locationHints'])->options,
            'gcbrFocus' => $props->getCheckboxById(self::SCHEMA['gcbrFocus'])->checked,
        ];

        $rules = [
            'id' => ['required', 'int'],
            'label' => ['required', 'string'],
            'description' => ['required'],
            'gcbrFocus' => ['required', 'boolean'],
            'link' => ['required', 'string', 'url'],
            'organizationType' => ['required', 'string'],
            'interventionFocuses' => ['required', 'array', function ($attribute, array $value, $fail) {
                $isTechnical = collect($value)->contains(fn (InterventionFocus $focus) => $focus->isMetaTechnicalFocus());
                $isGovernance = collect($value)->contains(fn (InterventionFocus $focus) => $focus->isMetaGovernanceFocus());

                if ((! $isTechnical && ! $isGovernance)) {
                    $fail('The entry must have at least either a [TECHNICAL] or [GOVERNANCE] focus, or both');
                }
            }],
            'activities' => ['required', 'array'],
            'location' => ['required', 'array'],
        ];

        if (self::$strict) {
            $rules = collect($rules)
                ->mapWithKeys(function ($previousRules, $key) {
                    return [$key => match ($key) {
                        'link' => [...$previousRules, new OkStatusRule],
                        default => $previousRules
                    }];
                })
                ->put('raw_link', ['required', 'string', 'url'])
                ->toArray();
        }

        $data = Validator::make($rawPage, $rules)->validate();

        $data['createdAt'] = $page->createdTime;
        $data['parentId'] = $parentId;
        $data['location'] = Location::fromNotionOptions($data['location']);
        $data['logo'] = Logosnatch::retrieve($data['link'], targetSize: 64);

        return new Entry(...$data);
    }
}

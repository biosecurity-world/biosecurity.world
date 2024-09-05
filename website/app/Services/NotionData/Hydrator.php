<?php

declare(strict_types=1);

namespace App\Services\NotionData;

use App\Services\Logosnatch\IconSnatch;
use App\Services\NotionData\DataObjects\Activity;
use App\Services\NotionData\DataObjects\Category;
use App\Services\NotionData\DataObjects\Entry;
use App\Services\NotionData\DataObjects\InterventionFocus;
use App\Services\NotionData\DataObjects\Location;
use App\Support\IdHash;
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
                    $errors[] = HydrationError::fromString('Entries must belong to a category.<', $page);

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
                    $this->categoryFromPage($page, -1) :
                    $this->entryFromPage($page, -1);
            } catch (ValidationException $e) {
                $errors[] = HydrationError::fromValidationException($e, $page);

                continue;
            }

            foreach ($parents as $parent) {
                $clone = clone $item;
                $clone->parentId = IdHash::hash($parent);
                $hydrated[] = $clone;

                if ($item instanceof Entry) {
                    if (! isset($entryCountMap[$clone->parentId])) {
                        $entryCountMap[$clone->parentId] = 0;
                    }

                    if (! isset($organizationTypeMap[$item->organizationType])) {
                        $organizationTypeMap[$item->organizationType] = 0;
                    }

                    $entryCountMap[$clone->parentId]++;
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

    public function categoryFromPage(Page $page, ?int $parentId): Category
    {
        $validated = Validator::validate(
            ['title' => $page->title()?->toString()],
            ['title' => ['required', 'string']]
        );

        return new Category(IdHash::hash($page->id), $parentId, $validated['title'], $page->createdTime);
    }

    public function entryFromPage(Page $page, int $parentId): Entry
    {
        $link = $page->properties()->getUrlById(self::SCHEMA['link'])->url;
        $rawPage = [
            'id' => IdHash::hash($page->id),
            'link' => ! str_starts_with($link ?? '', 'http') ? 'https://'.$link : $link,
            'label' => $page->title()?->toString(),
            'description' => $page->properties()->getRichTextById(self::SCHEMA['description']),
            'organizationType' => $page->properties()->getSelectById(self::SCHEMA['organizationType'])->option?->name,
            'activities' => array_map(function (SelectOption $opt) {
                return Activity::fromNotionOption($opt);
            }, $page->properties()->getMultiSelectById(self::SCHEMA['activityTypes'])->options),
            'interventionFocuses' => array_map(function (SelectOption $opt) {
                //                dump($opt);
                return InterventionFocus::fromNotionOption($opt);
            }, $page->properties()->getMultiSelectById(self::SCHEMA['interventionFocuses'])->options),
            'location' => $page->properties()->getMultiSelectById(self::SCHEMA['locationHints'])->options,
            'gcbrFocus' => $page->properties()->getCheckboxById(self::SCHEMA['gcbrFocus'])->checked,
        ];
        $data = Validator::make($rawPage, [
            'id' => ['required', 'int'],
            'label' => ['required', 'string'],
            'description' => ['required'],
            'gcbrFocus' => ['required', 'boolean'],
            'link' => ['required', 'string', 'url'],
            'organizationType' => ['required', 'string'],
            'interventionFocuses' => ['required', 'array', function ($attribute, array $value, $fail) {
                $isTechnical = collect($value)->contains(fn (InterventionFocus $focus) => $focus->isTechnical());
                $isGovernance = collect($value)->contains(fn (InterventionFocus $focus) => $focus->isGovernance());

                if ((! $isTechnical && ! $isGovernance)) {
                    $fail('The entry must have at least either a [TECHNICAL] or [GOVERNANCE] focus, or both');
                }
            }],
            'activities' => ['required', 'array'],
            'location' => ['required', 'array'],
        ])->validate();

        $data['createdAt'] = $page->createdTime;
        $data['parentId'] = $parentId;
        $data['location'] = Location::fromNotionOptions($data['location']);
        $data['logo'] = IconSnatch::retrieve($data['link']);

        return new Entry(...$data);
    }
}

<?php

use App\Services\NotionData\Enums\NodeType;
use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Node;
use App\Services\NotionData\Tree\TreeBuilder;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Notion $notion) {
    $pages = $notion->pages();
    ['tree' => $tree, 'lookup' => $lookup] = (new TreeBuilder())->build($pages);

    $dataByType = collect($lookup)->groupBy('@type');

    return view('welcome', [
        'databaseUrl' => $notion->databaseUrl(),
        'lastEditedAt' => $notion->lastEditedAt(),
        'activityTypes' => $notion->activityTypes(),
        'interventionFocus' => $notion->interventionFocuses(),
        'tree' => $tree,
        'categories' => $dataByType[NodeType::Category->value],
        'entrygroups' => $dataByType[NodeType::EntryGroup->value]
            ->map(function ($entrygroup) use ($lookup) {
                $entrygroup['entries'] = array_map(function ($entryId) use ($lookup) {
                    return $lookup[$entryId];
                }, $entrygroup['entries']);

                return $entrygroup;
            })
    ]);
})->name('welcome');

Route::get('/about')->name('about');
Route::get('/give-feedback')->name('give-feedback');
Route::get('/how-to-contribute')->name('how-to-contribute');


Route::get('/e/{id}/{entryId}', function (Notion $notion, string $id, string $entryId) {
    $pages = $notion->pages();
    ['tree' => $tree, 'lookup' => $lookup] = (new TreeBuilder())->build($pages);

    $entrygroup = null;

    $traverse = function (Node $current) use (&$traverse, $id, &$entrygroup) {
        if ($current->id === $id) {
            $entrygroup = $current;
            return;
        }

        foreach ($current->children as $child) {
            $traverse($child);
        }
    };

    $traverse($tree);

    $breadcrumbs = array_map(
        fn ($id) => $lookup[$id]['label'],
        $entrygroup->trail
    );

    $entry = $lookup[$entryId];
    $locations = collect($entry['locationHints'])->reverse()->pluck('name');

    $location = $locations->isNotEmpty() ?
        ($locations->contains('Global') ? 'Global' : $locations->implode(', ')) :
        null;



    return view('entry', [
        'entry' => $entry,
        'host' => parse_url($entry['link'], PHP_URL_HOST),
        'breadcrumbs' => $breadcrumbs,
        'organizationTypeNoun' => $notion->getOrganizationTypeNoun($entry['organizationType']),
        'notionUrl' => 'https://notion.so/' . str_replace('-', '', $entryId),
        'location' => $location,
    ]);
})->name('entries.show');


if (!app()->isProduction()) {
    Route::get('/entries', function (Notion $notion) {
        $pages = $notion->pages();
        $tree = (new TreeBuilder())->build($pages);

        $entrygroups = [];

        $traverse = function (Node $node) use ($tree, &$traverse, &$entrygroups) {
            if ($tree['lookup'][$node->id]['@type'] === NodeType::EntryGroup) {
                $entrygroups[] = array_map(
                    fn ($entryId) => $tree['lookup'][$entryId],
                    $tree['lookup'][$node->id]['entries']
                );
            }

            foreach ($node->children as $child) {
                $traverse($child);
            }
        };

        $traverse($tree['tree']);

        return view('test', ['entrygroups' => $entrygroups]);
    });

    // The code for rendering the tree could be an independent library
// but this isn't a priority for now, so some code is mixed with
// the code for the website which includes the code for testing the tree
// These routes are ignored by the crawler that builds the static version
// of this website.
    Route::get('/tree-rendering/{caseId}', function (string $caseId) {
        abort_if(!Cache::has('tree-' . $caseId), 404);

        $case = Cache::get('tree-'  . $caseId);

        return view('render-testcase', ['case' => $case]);
    })->name('tree-rendering');
}

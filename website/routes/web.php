<?php

declare(strict_types=1);

use App\Services\NotionData\Category;
use App\Services\NotionData\Entry;
use App\Services\NotionData\Entrygroup;
use App\Services\NotionData\Notion;
use App\Services\NotionData\Root;
use App\Services\NotionData\Tree\Node;
use App\Services\NotionData\Tree\TreeBuilder;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Notion $notion) {
    $pages = $notion->pages();
    $tree = (new TreeBuilder())->build($pages);
    $lookup = collect($tree['lookup']);

    $root = $lookup->first(fn ($el) => $el instanceof Root);
    $categories = $lookup->filter(fn ($el) => $el instanceof Category);
    $entries = $lookup->filter(fn ($el) => $el instanceof Entry);
    $entrygroups = $lookup->filter(fn ($el) => $el instanceof Entrygroup);

    return view('welcome', [
        'nodes' => $tree['nodes'],
        'lookup' => $lookup,
        'root' => $root,
        'categories' => $categories,
        'entries' => $entries,
        'entrygroups' => $entrygroups,
        'activities' => $entries->flatMap(fn (Entry $e) => $e->activities)->groupBy('id')->map(function ($group) {
            $activity = $group->first();
            $activity->total = $group->count();

            return $activity;
        })->values(),
        'interventionFocus' => $entries->flatMap(fn (Entry $e) => $e->interventionFocuses)->groupBy('id')->map(function ($group) {
            $focus = $group->first();
            $focus->total = $group->count();

            return $focus;
        })->values(),
        'databaseUrl' => $notion->databaseUrl(),
        'lastEditedAt' => \Carbon\Carbon::instance($notion->lastEditedAt()),
    ]);
})->name('welcome');

Route::get('/about', fn () => '')->name('about');
Route::get('/give-feedback', fn () => '')->name('give-feedback');
Route::get('/how-to-contribute', fn () => '')->name('how-to-contribute');

Route::get('/e/{id}/{entryId}', function (Notion $notion, int $id, int $entryId) {
    $pages = $notion->pages();
    $tree = (new TreeBuilder())->build($pages);
    $lookup = $tree['lookup'];

    $entrygroup = collect($tree['nodes'])->firstWhere('id', $id);
    abort_if(! $entrygroup instanceof Node, 404);

    /** @var Entry $entry */
    $entry = $lookup[$entryId];

    return view('entry', [
        'entry' => $entry,
        'host' => parse_url($entry->link, PHP_URL_HOST),
        'notionUrl' => sprintf('https://notion.so/%s', $entry->id),
        'breadcrumbs' => array_map(fn ($id) => $lookup[$id]->label, $entrygroup->trail),
    ]);
})->where('id', '\d+')->where('entryId', '\d+')->name('entries.show');

Route::get('/_/entries', function (Notion $notion) {
    $pages = $notion->pages();
    ['nodes' => $nodes, 'lookup' => $lookup] = (new TreeBuilder())->build($pages);

    $links = collect($nodes)
        ->filter(fn (Node $node) => $lookup[$node->id] instanceof Entrygroup)
        ->flatMap(function (Node $node) use ($lookup) {
            $group = $lookup[$node->id];

            return collect($group->entries)->map(function (int $entryId) use ($node) {
                return route('entries.show', ['id' => $node->id, 'entryId' => $entryId]);
            });
        });

    return view('entries.index', ['links' => $links]);
});

if (! app()->isProduction()) {
    // The code for rendering the tree could be an independent library
    // but this isn't a priority for now, so some code is mixed with
    // the code for the website which includes the code for testing the tree
    // These routes are ignored by the crawler that builds the static version
    // of this website.
    Route::get('/tree-rendering/{caseId}', function (string $caseId) {
        abort_if(! Cache::has('tree-'.$caseId), 404);

        $case = Cache::get('tree-'.$caseId);

        return view('render-testcase', ['case' => $case]);
    })->name('tree-rendering');
}

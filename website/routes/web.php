<?php

declare(strict_types=1);

use App\Services\NotionData\DataObjects\Activity;
use App\Services\NotionData\DataObjects\Entry;
use App\Services\NotionData\DataObjects\Entrygroup;
use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Notion $notion) {
    $tree = Tree::buildFromPages($notion->pages());

    return view('welcome', [
        'tree' => $tree,
        'lookup' => [
            'entries' => $tree->entries()->map(fn (Entry $entry) => [
                'id' => $entry->id,
                'activities' => $entry->getActivityBitmask(Activity::$seen),
                'lenses' => $entry->lens(),
            ]),
            'entrygroups' => $tree->entrygroups(),
        ],
        'databaseUrl' => $notion->databaseUrl(),
        'lastEditedAt' => Carbon::instance($notion->lastEditedAt()),
    ]);
})->name('welcome');

Route::get('/about', fn () => view('about'))->name('about');
Route::get('/give-feedback', fn () => '')->name('give-feedback');
Route::get('/how-to-contribute', fn () => '')->name('how-to-contribute');

Route::get('/e/{id}/{entryId}', function (Notion $notion, int $id, int $entryId) {
    $tree = Tree::buildFromPages($notion->pages());

    abort_if(! isset($tree->lookup[$id]) || ! isset($tree->lookup[$entryId]), 404);

    return view('entries.show', [
        'isXHR' => request()->header('X-Requested-With') === 'XMLHttpRequest',
        'entrygroup' => $tree->lookup[$id],
        'entry' => $tree->lookup[$entryId],
        'breadcrumbs' => $tree->getNodeById($id)->breadcrumbs($tree),
    ]);
})->where('id', '\d+')->where('entryId', '\d+')->name('entries.show');

Route::get('/_/entries', function (Notion $notion) {
    $tree = Tree::buildFromPages($notion->pages());

    $links = $tree
        ->entrygroups()
        ->flatMap(fn (Entrygroup $group) => collect($group->entries)->map(
            fn (int $entryId) => route('entries.show', ['id' => $group->id, 'entryId' => $entryId])
        ));

    return view('entries.index', ['links' => $links]);
});

if (app()->runningUnitTests()) {
    // The code for rendering the tree could be an independent library
    // but this isn't a priority for now, so some code is mixed with
    // the code for the website which includes the code for tests the tree
    // These routes are ignored by the crawler that builds the static version
    // of this website.
    Route::get('/tree-rendering/{caseId}', function (string $caseId) {
        abort_if(! Cache::has('tree-'.$caseId), 404);

        $case = Cache::get('tree-'.$caseId);

        return view('render-testcase', ['case' => $case]);
    })->name('tree-rendering');
}

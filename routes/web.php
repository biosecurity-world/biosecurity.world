<?php

declare(strict_types=1);

use App\Http\Controllers\ShowEntryController;
use App\Http\Controllers\ShowEntryPartialController;
use App\Http\Controllers\ShowWelcomeController;
use App\Services\NotionData\Models\Entrygroup;
use App\Services\NotionData\NotionClient;
use App\Services\NotionData\Tree\Tree;
use Dom\HTMLCollection;
use Illuminate\Support\Facades\Route;

Route::get('/', ShowWelcomeController::class)->name('welcome');
Route::redirect('/give-feedback', 'https://docs.google.com/forms/d/e/1FAIpQLSfJrpJ9o3xpIXOHgdOdkj_yrUt5LadIVbnzwKQk6tKWMuU5xw/viewform?usp=send_form')->name('give-feedback');
Route::redirect('/inclusion-criteria', 'https://docs.google.com/document/d/12JhGqx5PaA_jD0UKPDfWX4dfDp1gBoxdVM5tDTykPCA/edit?tab=t.0')->name('inclusion-criteria');
Route::view('/legal/privacy-policy', 'privacy')->name('privacy-policy');
Route::view('/legal/terms-of-service', 'terms-of-service')->name('terms-of-service');

Route::get('/entry/{id}/{slug}', ShowEntryController::class)->name('entries.show');
Route::get('/entry/{id}', function (NotionClient $notion, int $id) {
    $tree = Tree::buildFromPages($notion->pages());
    abort_if(! isset($tree->lookup[$id]), 404);
    $entry = $tree->lookup[$id];

    return redirect()->route('entries.show', ['id' => $id, 'slug' => $entry->slug()]);
});

Route::get('/partials/entries/{entryGroup}/{entryId}', ShowEntryPartialController::class)->name('partials.entry');
Route::get('/partials/map-content', function (NotionClient $notion) {
    return view('partials.map', [
        'tree' => Tree::buildFromPages($notion->pages()),
    ]);
});

Route::get('/_/entries', function (NotionClient $notion) {
    $tree = Tree::buildFromPages($notion->pages());

    $links = $tree
        ->entrygroups()
        ->flatMap(fn (Entrygroup $group) => collect($group->entries)->map(
            fn (int $entryId) => route('partials.entry', ['entryGroup' => $group->id, 'entryId' => $entryId])
        ))
        ->map(fn ($link) => <<<HTML
        <a href="$link">$link</a>
        HTML)
        ->join(PHP_EOL);

    return $links;
});

if (! app()->isProduction()) {
    // The code for rendering the tree could be an independent library
    // but this isn't a priority for now, so some code is mixed with
    // the code for the website which includes the code for tests the tree
    // These routes are ignored by the crawler that builds the static version
    // of this website.
    Route::get('/tree-rendering/{caseId}', function (string $caseId) {
        abort_if(! cache()->has('tree-'.$caseId), 404);

        $case = cache()->get('tree-'.$caseId);

        return view('render-testcase', ['case' => $case]);
    })->name('tree-rendering');
}

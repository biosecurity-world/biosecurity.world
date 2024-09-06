<?php

declare(strict_types=1);

use App\Http\Controllers\ShowEntryPartialController;
use App\Http\Controllers\ShowMapPartialController;
use App\Http\Controllers\ShowWelcomeController;
use App\Services\NotionData\DataObjects\Entrygroup;
use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;
use Illuminate\Support\Facades\Route;

Route::get('/', ShowWelcomeController::class)->name('welcome');
Route::view('/about', 'about')->name('about');
Route::get('/give-feedback', fn () => '')->name('give-feedback');
Route::get('/how-to-contribute', fn () => '')->name('how-to-contribute');
Route::view('/legal/privacy-policy', 'privacy')->name('privacy-policy');
Route::view('/legal/terms-of-service', 'terms-of-service')->name('terms-of-service');
Route::get('/e/{id}/{entryId}', ShowEntryPartialController::class)->name('entries.show');
Route::get('/m', ShowMapPartialController::class);
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
        abort_if(! cache()->has('tree-'.$caseId), 404);

        $case = cache()->get('tree-'.$caseId);

        return view('render-testcase', ['case' => $case]);
    })->name('tree-rendering');
}

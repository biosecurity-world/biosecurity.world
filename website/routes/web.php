<?php

use Illuminate\Support\Facades\Route;
use App\Services\NotionData\NotionWrapper;

Route::get('/data.json', function (NotionWrapper $notion) {
    $pages = $notion->pages();
    $graph = (new App\Services\NotionData\GraphBuilder\Builder($pages))->build();

    return response()->json($graph);
});

Route::get('/', function (NotionWrapper $notion) {
    return view('welcome', [
        'databaseUrl' => $notion->databaseUrl(),
        'lastModifiedAt' => now(),
    ]);
});

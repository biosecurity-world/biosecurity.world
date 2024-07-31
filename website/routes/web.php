<?php

use App\Services\NotionData\Graph\GraphBuilder;
use App\Services\NotionData\Notion;
use Illuminate\Support\Facades\Route;

Route::get('/data.json', function (Notion $notion) {
    $pages = $notion->pages();
    $graph = \Illuminate\Support\Facades\Cache::rememberForever('graph', fn () => (new GraphBuilder($pages))->build());

    return response()->json($graph);
});

Route::get('/', function (Notion $notion) {
    return view('welcome', [
        'databaseUrl' => $notion->databaseUrl(),
        'lastEditedAt' => $notion->lastEditedAt(),
        'activityTypes' => $notion->activityTypes(),
        'interventionFocus' => $notion->interventionFocuses()
    ]);
});

<?php

namespace App\Http\Controllers;

use App\Services\NotionData\NotionClient;
use App\Services\NotionData\Tree\Tree;
use Illuminate\Contracts\View\View;

class ShowMapPartialController
{
    public function __invoke(NotionClient $notion): View
    {
        return view('partials.map', [
            'tree' => Tree::buildFromPages($notion->pages()),
        ]);
    }
}

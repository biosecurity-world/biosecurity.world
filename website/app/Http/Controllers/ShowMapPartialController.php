<?php

namespace App\Http\Controllers;

use App\Services\NotionData\Notion;
use App\Services\NotionData\Tree\Tree;

class ShowMapPartialController
{
    public function __invoke(Notion $notion)
    {
        $tree = Tree::buildFromPages($notion->pages());

        return view('partials.map', [
         'tree' => $tree,
     ]);
    }
}

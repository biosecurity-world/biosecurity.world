<?php

namespace App\Services\NotionData;

use App\Services\NotionData\Enums\PageType;

class Page
{
    public function __construct(
        public PageType $type,
        public string   $id,
        public array    $parents,
        public array    $data
    )
    {
    }
}

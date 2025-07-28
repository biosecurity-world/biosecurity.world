<?php

namespace App\Services\NotionData;

use App\Services\NotionData\Models\Category;
use App\Services\NotionData\Models\Entry;
use Notion\Pages\Page;

class HydrationError
{
    public function __construct(
        public Page|Entry|Category $page,
        public string $message
    ) {}
}

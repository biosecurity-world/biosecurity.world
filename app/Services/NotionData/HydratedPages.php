<?php

namespace App\Services\NotionData;

use App\Services\NotionData\Models\Category;
use App\Services\NotionData\Models\Entry;

class HydratedPages
{
    public function __construct(
        /** @var array<Category|Entry> */
        public array $data,
        /** @var HydrationError[] */
        public array $errors
    ) {}
}

<?php

namespace App\Services\NotionData\Models;

class Category
{
    public function __construct(
        public string $id,
        public string $label,
        public array  $parents = []
    )
    {
    }
}

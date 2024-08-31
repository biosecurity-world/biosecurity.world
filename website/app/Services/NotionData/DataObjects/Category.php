<?php

declare(strict_types=1);

namespace App\Services\NotionData\DataObjects;

#[\AllowDynamicProperties]
class Category
{
    public function __construct(
        public string $id,
        public ?string $parentId,
        public string $label,
    ) {}
}

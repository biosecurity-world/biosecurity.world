<?php

declare(strict_types=1);

namespace App\Services\NotionData\DataObjects;

#[\AllowDynamicProperties]
class Category
{
    public function __construct(
        public int $id,
        public ?int $parentId,
        public string $label,
        public \DateTimeInterface $createdAt,
    ) {}
}

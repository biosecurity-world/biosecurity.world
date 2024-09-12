<?php

declare(strict_types=1);

namespace App\Services\NotionData\DataObjects;

class Category
{
    public function __construct(
        public int $id,
        public ?int $parentId,
        public string $label,
        public \DateTimeInterface $createdAt,
    ) {}
}

<?php

namespace App\Services\NotionData;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
class Category implements Arrayable
{
    public function __construct(
        public string $id,
        public ?string $parentId,
        public string $label,
    )
    {
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'parentId' => $this->parentId,
            'label' => $this->label,
        ];
    }
}

<?php

namespace App\Services\NotionData;

use App\Services\Iconsnatch\Logo;
use Illuminate\Contracts\Support\Arrayable;
use Notion\Databases\Properties\SelectOption;

/**
 * @implements Arrayable<string, mixed>
 */
class Entry implements Arrayable
{
    public function __construct(
        public string $id,
        public string $parentId,
        public string $label,
        public string $link,
        public string $description,
        public string $organizationType,
        /** @var SelectOption[] */
        public array  $interventionFocuses,
        /** @var SelectOption[] */
        public array  $activityTypes,
        /** @var SelectOption[] */
        public array  $locationHints,
        public bool   $gcbrFocus,
        public ?Logo   $logo,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parentId' => $this->parentId,
            'label' => $this->label,
            'link' => $this->link,
            'description' => $this->description,
            'organizationType' => $this->organizationType,
            'interventionFocuses' => $this->interventionFocuses,
            'activityTypes' => $this->activityTypes,
            'locationHints' => $this->locationHints,
            'gcbrFocus' => $this->gcbrFocus,
            'logo' => $this->logo,
        ];
    }
}

<?php

namespace App\Services\NotionData\Models;

use App\Services\NotionData\Support\Icon;

class Entry
{
    public function __construct(
        public string $id,

        public string $label,

        public string $link,

        public string $description,

        public string $organizationType,

        public array  $interventionFocuses,
        public array  $activityTypes,

        public array  $locationHints,

        public bool   $gcbrFocus,

        public ?Icon  $logo = null,

        public array  $parents = [],
    )
    {
    }
}

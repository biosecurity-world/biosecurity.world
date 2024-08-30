<?php

namespace App\Services\NotionData;

use App\Services\NotionData\Enums\NotionColor;
use Notion\Databases\Properties\SelectOption;

#[\AllowDynamicProperties]
class InterventionFocus
{
    public function __construct(
        public string $id,
        public string $label,
        public NotionColor $color
    ) {}

    public static function fromNotionOption(SelectOption $opt): InterventionFocus
    {
        if (is_null($opt->id) || is_null($opt->name)) {
            throw new \InvalidArgumentException('Select for the activity is missing either an id or a name');
        }

        return new self(
            $opt->id,
            $opt->name,
            NotionColor::from($opt->color?->value ?? NotionColor::Default->value)
        );
    }

    public function isTechnical(): bool {
        return $this->label === '[TECHNICAL]';
    }

    public function isGovernance(): bool {
        return $this->label === '[GOVERNANCE]';
    }
}

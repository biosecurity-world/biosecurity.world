<?php

namespace App\Services\NotionData\DataObjects;

use App\Services\NotionData\Enums\NotionColor;
use Notion\Databases\Properties\SelectOption;

class Activity
{
    public function __construct(
        public string $id,
        public string $label,
        public NotionColor $color
    ) {}

    public static function fromNotionOption(SelectOption $opt): Activity
    {
        if (is_null($opt->id) || is_null($opt->name)) {
            throw new \InvalidArgumentException('Select for the activity is missing either an id or a name');
        }

        return new self($opt->id, $opt->name, NotionColor::from($opt->color?->value ?? NotionColor::Default->value));
    }

    public function iconName(): ?string
    {
        return match ($this->label) {
            'Coordination / strategy' => 'strategy',
            'Lobbying' => 'lobbying',
            'Funding / philanthropy' => 'funding',
            'Research' => 'research',
            'Technology development' => 'technology',
            'Policy development / consultancy' => 'policy',
            'Public advocacy / campaigning / outreach' => 'advocacy',
            'Education / career support' => 'education',
            default => null,
        };
    }
}

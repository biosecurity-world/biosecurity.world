<?php

namespace App\Services\NotionData\DataObjects;

use App\Services\NotionData\Enums\NotionColor;
use Notion\Databases\Properties\SelectOption;

class InterventionFocus
{
    /** @var array<string> */
    protected static array $seen = [];

    public const string TECHNICAL_FOCUS_ID = '|tSq';

    public const string GOVERNANCE_FOCUS_ID = 'rBTY]';

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

        if (! in_array($opt->id, self::$seen)) {
            self::$seen[] = $opt->id;
        }

        return new self(
            $opt->id,
            $opt->name,
            NotionColor::from($opt->color?->value ?? NotionColor::Default->value)
        );
    }

    public static function totalSeen(): int
    {
        return count(self::$seen);
    }

    public function isTechnical(): bool
    {
        return $this->id === self::TECHNICAL_FOCUS_ID;
    }

    public function isGovernance(): bool
    {
        return $this->label === self::GOVERNANCE_FOCUS_ID;
    }
}

<?php

namespace App\Services\NotionData\DataObjects;

use App\Services\NotionData\Enums\NotionColor;
use App\Support\IdMap;
use Notion\Databases\Properties\SelectOption;

class InterventionFocus
{
    /** @var array<string> */
    protected static array $seen = [];

    public const string TECHNICAL_META_FOCUS_ID = '|tSq';

    public const string GOVERNANCE_META_FOCUS_ID = 'rBTY';

    public function __construct(
        public int $id,
        public string $label,
        public NotionColor $color
    ) {}

    public static function fromNotionOption(SelectOption $opt): InterventionFocus
    {
        if (is_null($opt->id) || is_null($opt->name)) {
            throw new \InvalidArgumentException('Select for the activity is missing either an id or a name');
        }

        $id = IdMap::hash($opt->id);

        if (! in_array($id, self::$seen)) {
            self::$seen[] = $opt->id;
        }

        return new self($id, $opt->name, NotionColor::from($opt->color?->value ?? NotionColor::Default->value));
    }

    public static function totalSeen(): int
    {
        return count(self::$seen);
    }

    /** @return bool Is the entry the [TECHNICAL] focus? */
    public function isMetaTechnicalFocus(): bool
    {
        return IdMap::find($this->id) === self::TECHNICAL_META_FOCUS_ID;
    }

    /** @return bool Is the entry the [GOVERNANCE] focus? */
    public function isMetaGovernanceFocus(): bool
    {
        return IdMap::find($this->id) === self::GOVERNANCE_META_FOCUS_ID;
    }
}

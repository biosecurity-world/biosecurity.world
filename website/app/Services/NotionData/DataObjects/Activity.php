<?php

namespace App\Services\NotionData\DataObjects;

use App\Services\NotionData\Enums\NotionColor;
use App\Support\IdMap;
use Notion\Databases\Properties\SelectOption;

class Activity
{
    /** @var array<int> */
    public static array $seen = [];

    /** @var array<int> */
    public static array $countById = [];

    public function __construct(
        public int $id,
        public string $label,
        public NotionColor $color
    ) {}

    public static function totalSeen(): int
    {
        return count(self::$seen);
    }

    public static function fromNotionOption(SelectOption $opt): Activity
    {
        if (is_null($opt->id) || is_null($opt->name)) {
            throw new \InvalidArgumentException('Select for the activity is missing either an id or a name');
        }

        $id = IdMap::hash($opt->id);

        if (! in_array($id, self::$seen)) {
            self::$seen[] = $id;

            sort(self::$seen);
        }

        if (! isset(self::$countById[$id])) {
            self::$countById[$id] = 0;
        }

        self::$countById[$id]++;

        return new self(
            $id,
            $opt->name,
            NotionColor::from($opt->color?->value ?? NotionColor::Default->value)
        );
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

    public function occurrences(): int
    {
        return self::$countById[$this->id];
    }
}

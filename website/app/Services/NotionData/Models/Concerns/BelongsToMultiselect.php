<?php

namespace App\Services\NotionData\Models\Concerns;

use App\Services\NotionData\Enums\NotionColor;
use App\Support\IdMap;
use Illuminate\Support\Collection;
use Notion\Databases\Properties\SelectOption;

trait BelongsToMultiselect
{
    public function __construct(
        public int $id,
        public string $label,
        public NotionColor $color
    ) {}

    /** @var array<int> */
    protected static array $seen = [];

    /** @var array<int> */
    protected static array $countById = [];

    public static function fromNotionOption(SelectOption $opt): self
    {
        if (is_null($opt->id) || is_null($opt->name)) {
            throw new \InvalidArgumentException(sprintf('Option for the multiselect [%s] is missing either an id or a name', self::class));
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

    /** @return Collection<int, int> */
    public static function all(): Collection
    {
        return collect(self::$seen);
    }

    public function occurrences(): int
    {
        return self::$countById[$this->id];
    }
}

<?php

namespace App\Services\NotionData;

use Notion\Databases\Properties\SelectOption;

class Location implements \Stringable
{
    public function __construct(
        /** @var array<string> */
        public array $hints
    ) {}

    /** @param array<SelectOption> $locationHints */
    public static function fromNotionOptions(array $locationHints): self
    {
        return new self(
            /** @phpstan-ignore-next-line  */
            collect($locationHints)
                ->map->name
                ->filter()
                ->toArray()
        );
    }

    public function toString(): string
    {
        if (in_array('Global', $this->hints)) {
            return 'Global';
        }

        return collect($this->hints)
            ->reverse()
            ->pluck('name')
            ->map(function ($name) {
                if ($name === 'Europe (excl. UK)') {
                    return 'Europe';
                }

                return $name;
            })
            ->implode(', ');
    }

    public function __toString()
    {
        return $this->toString();
    }
}

<?php

namespace App\Services\NotionData\DataObjects;

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
        $hints = [];

        foreach ($locationHints as $locationHint) {
            if (is_null($locationHint->name)) {
                continue;
            }

            $hints[] = $locationHint->name;
        }

        return new self($hints);
    }

    public function toString(): string
    {
        if (in_array('Global', $this->hints)) {
            return 'Global';
        }

        return implode(', ', array_reverse($this->hints));
    }

    public function __toString()
    {
        return $this->toString();
    }
}

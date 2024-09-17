<?php

namespace App\Services\NotionData\Enums;

enum DomainEnum: string
{
    case Technical = '|tSq';
    case Governance = 'rBTY';

    public function mask(): int
    {
        return match ($this) {
            self::Technical => 1,
            self::Governance => 2,
        };
    }
}

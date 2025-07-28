<?php

namespace App\Services\NotionData\Enums;

use App\Services\NotionData\Hydrator;

enum DomainEnum: string
{
    case Technical = Hydrator::TECHNICAL_DOMAIN;
    case Governance = Hydrator::GOVERNANCE_DOMAIN;

    public function mask(): int
    {
        return match ($this) {
            self::Technical => 1,
            self::Governance => 2,
        };
    }
}

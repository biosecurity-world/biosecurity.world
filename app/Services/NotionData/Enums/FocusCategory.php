<?php

namespace App\Services\NotionData\Enums;

enum FocusCategory: int
{
    case SafeEthicalResearch = 1;
    case Surveillance = 2;
    case Therapies = 3;
    case Preparedness = 4;
    case EmergingBiotechnologies = 5;

    public function label(): string
    {
        return match ($this) {
            self::SafeEthicalResearch => 'Safe and ethical research',
            self::Surveillance => 'Surveillance and diagnostics',
            self::Therapies => 'Therapies',
            self::Preparedness => 'Preparedness',
            self::EmergingBiotechnologies => 'Emerging biotechnologies'
        };
    }
}

<?php

namespace App\Services\NotionData\Enums;

enum FocusCategory: int
{
    case UpstreamInterventions = 1;
    case DetectionAndEarlyWarning = 2;
    case ResponseAndConsequenceManagement = 3;
    case Uncategorized = 4;

    public function label(): string
    {
        return match ($this) {
            self::UpstreamInterventions => 'Preventing and reducing threat emergence',
            self::DetectionAndEarlyWarning => 'Detection and early warning',
            self::ResponseAndConsequenceManagement => 'Response and consequence management',
            self::Uncategorized => 'Uncategorized'
        };
    }
}

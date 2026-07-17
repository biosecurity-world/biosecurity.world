<?php

declare(strict_types=1);

namespace App\Services\NotionData\Enums;

enum LocationRegion: string
{
    case USA = 'USA';
    case UnitedKingdom = 'United Kingdom';
    case Europe = 'Europe (excl. UK)';
    case EastAsiaPacific = 'East Asia & Pacific';
    case SouthAsia = 'South Asia';
    case CentralAsia = 'Central Asia';
    case MiddleEast = 'Middle East';
    case NorthAfrica = 'North Africa';
    case SubSaharanAfrica = 'Sub-Saharan Africa';
    case NorthAmericaExclUS = 'North America (excl. US)';
    case SouthAmerica = 'South America';

    /** Location labels shown as standalone pills next to the "Locations" header. */
    public const array TOP_LEVEL_LABELS = ['Global', 'Remote'];

    public function label(): string
    {
        return $this->value;
    }

    /**
     * Location labels that belong to this region (non-region locations only).
     *
     * @return list<string>
     */
    public function childLabels(): array
    {
        return match ($this) {
            self::USA => [
                'Albuquerque NM', 'Arlington VA', 'Atlanta GA', 'Baltimore',
                'Bethesda MD', 'Boston MA', 'Boulder CO', 'Broomfield CO', 'Cambridge MA',
                'Cavendish VT', 'Chicago IL', 'College Station', 'Gaithersburg MD',
                'Lexington MA', 'Livermore CA', 'Los Alamos NM', 'Minneapolis MN',
                'Monterey CA', 'New York City', 'Omaha NE', 'Pittsburgh PA', 'Providence RI',
                'San Francisco CA', 'Santa Monica CA', 'Seattle WA', 'Stanford CA',
                'Washington DC',
            ],
            self::UnitedKingdom => [
                'Cambridge', 'Essex', 'Liverpool', 'London', 'Oxford',
            ],
            self::Europe => [
                'Amsterdam', 'Austria', 'Barcelona', 'Basel', 'Belgium',
                'Berlin', 'Bern', 'Brussels', 'France', 'Frankfurt',
                'Geneva', 'Germany', 'Grenoble', 'Hamburg', 'Heidelberg',
                'Italy', 'Madrid', 'Munich', 'Netherlands', 'Norway',
                'Oslo', 'Paris', 'Rome', 'Rotterdam', 'Spain',
                'Stockholm', 'Sweden', 'Switzerland', 'Trieste', 'Vienna', 'Zug',
            ],
            self::EastAsiaPacific => [
                'Australia', 'Beijing', 'Canberra', 'China', 'Singapore',
            ],
            self::SouthAsia => [
                'India', 'Bengaluru', 'Hyderabad',
            ],
            self::CentralAsia => [],
            self::MiddleEast => ['Israel'],
            self::NorthAfrica => [],
            self::SubSaharanAfrica => [],
            self::NorthAmericaExclUS => ['Canada', 'Mexico'],
            self::SouthAmerica => ['Colombia'],
        };
    }

    /**
     * Map a location label to its parent region.
     */
    public static function fromLocationLabel(string $label): ?self
    {
        foreach (self::cases() as $region) {
            if (in_array($label, $region->childLabels(), true)) {
                return $region;
            }
        }

        return null;
    }

    /**
     * The location label that serves as the region header itself.
     * Every region has a corresponding LocationHint with the same label.
     */
    public function headerLocationLabel(): string
    {
        return $this->value;
    }
}

<?php

namespace App\Services\NotionData\Models;

use App\Services\NotionData\Enums\LocationRegion;
use App\Services\NotionData\Models\Concerns\BelongsToMultiselect;

class LocationHint
{
    use BelongsToMultiselect;

    public function region(): ?LocationRegion
    {
        if ($this->isTopLevel()) {
            return null;
        }

        // Check if this location IS a region header
        foreach (LocationRegion::cases() as $region) {
            if ($region->headerLocationLabel() === $this->label) {
                return $region;
            }
        }

        // Otherwise find which region this location belongs to
        return LocationRegion::fromLocationLabel($this->label);
    }

    public function isTopLevel(): bool
    {
        return in_array($this->label, LocationRegion::TOP_LEVEL_LABELS, true);
    }

    public function isRegionHeader(): bool
    {
        foreach (LocationRegion::cases() as $region) {
            if ($region->headerLocationLabel() === $this->label) {
                return true;
            }
        }

        return false;
    }

    private const array COUNTRIES = [
        'Australia', 'Austria', 'Belgium', 'Canada', 'China', 'Colombia',
        'France', 'Germany', 'India', 'Israel', 'Italy',
        'Mexico', 'Netherlands', 'Norway', 'Singapore', 'Spain',
        'Sweden', 'Switzerland',
    ];

    /** Maps city labels to their parent country label. */
    private const array CITY_TO_COUNTRY = [
        // Europe
        'Vienna' => 'Austria',
        'Brussels' => 'Belgium',
        'Paris' => 'France',
        'Grenoble' => 'France',
        'Berlin' => 'Germany',
        'Frankfurt' => 'Germany',
        'Hamburg' => 'Germany',
        'Heidelberg' => 'Germany',
        'Munich' => 'Germany',
        'Rome' => 'Italy',
        'Trieste' => 'Italy',
        'Amsterdam' => 'Netherlands',
        'Rotterdam' => 'Netherlands',
        'Oslo' => 'Norway',
        'Barcelona' => 'Spain',
        'Madrid' => 'Spain',
        'Stockholm' => 'Sweden',
        'Basel' => 'Switzerland',
        'Bern' => 'Switzerland',
        'Geneva' => 'Switzerland',
        'Zug' => 'Switzerland',
        // East Asia & Pacific
        'Beijing' => 'China',
        'Canberra' => 'Australia',
        // South Asia
        'Bengaluru' => 'India',
        'Hyderabad' => 'India',
    ];

    public function isCountry(): bool
    {
        return in_array($this->label, self::COUNTRIES, true);
    }

    public function parentCountryLabel(): ?string
    {
        return self::CITY_TO_COUNTRY[$this->label] ?? null;
    }

    public function globalSortOrder(): int
    {
        $offset = self::all()->search(fn (int $id) => $id === $this->id);

        if ($offset === false) {
            throw new \LogicException('Location hint is missing from its own registry.');
        }

        return $offset;
    }
}

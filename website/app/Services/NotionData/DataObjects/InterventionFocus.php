<?php

namespace App\Services\NotionData\DataObjects;

use App\Services\NotionData\Enums\NotionColor;
use App\Support\IdHash;
use Notion\Databases\Properties\SelectOption;

class InterventionFocus
{
    /** @var array<string> */
    protected static array $seen = [];

    public const string TECHNICAL_META_FOCUS_ID = '|tSq';

    public const string GOVERNANCE_META_FOCUS_ID = 'rBTY';

    public function __construct(
        public int $id,
        public string $label,
        public NotionColor $color
    ) {}

    public static function fromNotionOption(SelectOption $opt): InterventionFocus
    {
        if (is_null($opt->id) || is_null($opt->name)) {
            throw new \InvalidArgumentException('Select for the activity is missing either an id or a name');
        }

        $id = IdHash::hash($opt->id);

        if (! in_array($id, self::$seen)) {
            self::$seen[] = $opt->id;
        }

        return new self($id, $opt->name, NotionColor::from($opt->color?->value ?? NotionColor::Default->value));
    }

    public static function totalSeen(): int
    {
        return count(self::$seen);
    }

    /**
     * @return bool Is the entry the [TECHNICAL] focus?
     */
    public function isMetaTechnicalFocus(): bool
    {
        return IdHash::reverse($this->id) === self::TECHNICAL_META_FOCUS_ID;
    }

    /**
     * @return bool Is the entry the [GOVERNANCE] focus?
     */
    public function isMetaGovernanceFocus(): bool
    {
        return IdHash::reverse($this->id) === self::GOVERNANCE_META_FOCUS_ID;
    }

    public function belongsToTechnicalMetaFocus(): bool
    {
        return in_array(IdHash::reverse($this->id), [
            'ZXKA', // Vaccines
            'e:NA', // Rapid diagnostics
            'SV{{', // Digital detection
            'k\\\\W', // Indoor air quality
            'hop{', // Therapeutics
            'CNRk', // Antimicrobial resistance
            'Yjy:', // DNA synthesis / screening

            // TODO: TO BE CONFIRMED
            '83c6fe3d-7d6f-4f25-a203-53f4bde05575', // Epidemiology
            'APQ~', // Pathogen surveillance
            'K~O{', // Synthetic biology
            'f815b152-f213-4e99-bcfa-830e1143e59c', // AI x Bio
            '{[Rj', // Personal protective equipment
        ]);
    }

    public function belongsToGovernanceMetaFocus(): bool
    {
        return in_array(IdHash::reverse($this->id), [
            'CNRk', // Antimicrobial resistance
            'h_T{', // Cyberbiosecurity
            '>SYG', // Lab biosafety
            'Yjy:', // DNA synthesis / screening
            '|_z{', // ePPP research / DURC
            'hiAX', // Biological weapons / Non-proliferation

            // TODO: TO BE CONFIRMED
            '83c6fe3d-7d6f-4f25-a203-53f4bde05575', // Epidemiology
            '8fa4d53b-aa93-49ef-af76-32940fb918e7',  // Crisis management

        ]);
    }
}

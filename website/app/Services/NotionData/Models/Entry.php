<?php

declare(strict_types=1);

namespace App\Services\NotionData\Models;

use App\Services\Logosnatch\Logo;
use App\Support\IdMap;
use Illuminate\Support\Collection;
use Notion\Pages\Properties\RichTextProperty;

class Entry
{
    /** @var float Defined as 1 / (number of occurrences in the entrygroups) */
    public float $uniqueness;

    /** @var float Defined as 1 / (number of occurrences in the entrygroups) */
    public float $organizationTypeUniqueness;

    public function __construct(
        public int $id,
        public int $parentId,
        public string $label,
        public \DateTimeInterface $createdAt,
        public string $link,
        public RichTextProperty $description,
        public string $organizationType,
        /** @var Collection<int,InterventionFocus> */
        public Collection $interventionFocuses,
        /** @var Collection<int,Activity> */
        public Collection $activities,
        /** @var Collection<int,LocationHint> */
        public Collection $locationHints,
        public bool $focusesOnGCBRs,
        public Logo $logo,
    ) {}

    public function nounForOrganizationType(): string
    {
        return match ($this->organizationType) {
            'For-profit company' => 'company',
            'Think tank' => 'think tank',
            'Government' => 'governmental organization',
            'Intergovernmental agency' => 'intergovernmental agency',
            'National non-profit organization' => 'national NGO',
            'International non-profit organization' => 'international NGO',
            'Media' => 'media organization',
            'Research institute / lab / network' => 'research institute',
            default => throw new \RuntimeException('Unknown organization type: '.$this->organizationType),
        };
    }

    public function notionUrl(): string
    {
        return sprintf('https://notion.so/%s', str_replace('-', '', IdMap::find($this->id)));
    }

    public function host(): string
    {
        $host = parse_url($this->link, PHP_URL_HOST);

        if (! is_string($host)) {
            throw new \RuntimeException('Should not happen: could not parse host in entry link but the entry should have been validated');
        }

        return $host;
    }

    public function belongsToTechnicalDomain(): bool
    {
        return $this->interventionFocuses->contains(fn (InterventionFocus $focus) => $focus->isMetaTechnicalFocus());
    }

    public function belongsToGovernanceDomain(): bool
    {
        return $this->interventionFocuses->contains(fn (InterventionFocus $focus) => $focus->isMetaGovernanceFocus());
    }

    public function getActivitiesBitmask(): int
    {
        return Activity::all()->reduce(function ($mask, $id, $offset) {
            return $mask | ($this->activities->contains('id', $id) << $offset);
        }, 0);
    }

    public function getFocusesBitmask(): int
    {
        return InterventionFocus::all()->reduce(function ($mask, $id, $offset) {
            return $mask | ($this->interventionFocuses->contains('id', $id) << $offset);
        }, 0);
    }

    public function getDomainBitmask(): int
    {
        return ($this->belongsToTechnicalDomain() ? 1 : 0) | ($this->belongsToGovernanceDomain() ? 1 : 0) << 1;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\NotionData\DataObjects;

use App\Services\Logosnatch\Logo;
use App\Support\IdMap;
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
        /** @var array<InterventionFocus> */
        public array $interventionFocuses,
        /** @var array<Activity> */
        public array $activities,
        public Location $location,
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
            default => 'organization',
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
            throw new \RuntimeException('Should not happen: could not parse host in entry link but entries are supposed to be always validated');
        }

        return $host;
    }

    public function hasActivity(int $id): bool
    {
        foreach ($this->activities as $activity) {
            if ($activity->id === $id) {
                return true;
            }
        }

        return false;
    }

    public function belongsToTechnicalDomain(): bool
    {
        foreach ($this->interventionFocuses as $focus) {
            if ($focus->isMetaTechnicalFocus()) {
                return true;
            }
        }

        return false;
    }

    public function belongsToGovernanceDomain(): bool
    {
        foreach ($this->interventionFocuses as $focus) {
            if ($focus->isMetaGovernanceFocus()) {
                return true;
            }
        }

        return false;
    }

    public function getActivitiesBitmask(): int
    {
        $mask = 0;
        $offset = 0;

        foreach (Activity::$seen as $id) {
            $mask |= $this->hasActivity($id) << $offset++;
        }

        return $mask;
    }

    public function getDomainBitmask(): int
    {
        return ($this->belongsToTechnicalDomain() ? 1 : 0) | ($this->belongsToGovernanceDomain() ? 1 : 0) << 1;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\NotionData;

use App\Services\Iconsnatch\Logo;
use Notion\Pages\Properties\RichTextProperty;

#[\AllowDynamicProperties]
class Entry
{
    public function __construct(
        public string $id,
        public string $parentId,
        public string $label,
        public string $link,
        public RichTextProperty $description,
        public string $organizationType,
        /** @var array<InterventionFocus> */
        public array $interventionFocuses,
        /** @var array<Activity> */
        public array $activities,
        public Location $location,
        public bool $gcbrFocus,
        public ?Logo $logo,
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
}

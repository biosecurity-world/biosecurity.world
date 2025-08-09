<?php

namespace App\Services\NotionData\Models;

use App\Services\NotionData\Models\Concerns\BelongsToMultiselect;

class Activity
{
    use BelongsToMultiselect;

    public function iconName(): ?string
    {
        return match ($this->label) {
            'Coordination' => 'strategy',
            'Lobbying' => 'lobbying',
            'Funding' => 'funding',
            'Research' => 'research',
            'Technology development' => 'technology',
            'Policy development' => 'policy',
            'Outreach & Advocacy' => 'advocacy',
            'Education & Career' => 'education',
            default => throw new \InvalidArgumentException('Unknown activity label: '.$this->label),
        };
    }
}

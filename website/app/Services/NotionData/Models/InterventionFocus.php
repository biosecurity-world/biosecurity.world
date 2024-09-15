<?php

namespace App\Services\NotionData\Models;

use App\Services\NotionData\Models\Concerns\BelongsToMultiselect;
use App\Support\IdMap;

class InterventionFocus
{
    use BelongsToMultiselect;

    public const string TECHNICAL_META_FOCUS_ID = '|tSq';

    public const string GOVERNANCE_META_FOCUS_ID = 'rBTY';

    /** @return bool Is the entry the [TECHNICAL] focus? */
    public function isMetaTechnicalFocus(): bool
    {
        return IdMap::find($this->id) === self::TECHNICAL_META_FOCUS_ID;
    }

    /** @return bool Is the entry the [GOVERNANCE] focus? */
    public function isMetaGovernanceFocus(): bool
    {
        return IdMap::find($this->id) === self::GOVERNANCE_META_FOCUS_ID;
    }
}

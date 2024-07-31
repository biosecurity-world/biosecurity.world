<?php

namespace App\Services\NotionData\Enums;

enum PageType: string
{
    case Entry = 'entry';
    case Category = 'category';
    case EntryGroup = 'entryGroup';
}

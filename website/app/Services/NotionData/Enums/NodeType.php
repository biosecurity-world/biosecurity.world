<?php

namespace App\Services\NotionData\Enums;

enum NodeType: string
{
    case Entry = 'entry';
    case Category = 'category';
    case EntryGroup = 'entry_group';
}

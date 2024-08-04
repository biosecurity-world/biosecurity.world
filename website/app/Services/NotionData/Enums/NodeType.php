<?php

namespace App\Services\NotionData\Enums;

enum NodeType: int
{
    case Entry = 1;
    case Category = 2;
    case EntryGroup = 3;
    case Root = 4;
}

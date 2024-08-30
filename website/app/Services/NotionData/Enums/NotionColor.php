<?php

namespace App\Services\NotionData\Enums;

enum NotionColor: string
{
    case Default = "default";
    case Gray = "gray";
    case Red = "red";
    case Pink = "pink";
    case Purple = "purple";
    case Blue = "blue";
    case Green = "green";
    case Yellow = "yellow";
    case Orange = "orange";
    case Brown = "brown";

    public function foreground(): string {
        return match ($this) {
            self::Default => "#37352F",
            self::Gray => "#9B9A97",
            self::Red => "#E03E3E",
            self::Pink => "#AD1A72",
            self::Purple =>"#6940A5" ,
            self::Blue => "#0B6E99",
            self::Green => "#0F7B6C",
            self::Yellow => "#DFAB01",
            self::Orange => "#D9730D",
            self::Brown => "#64473A",
        };
    }

    public function background(): string {
        return match ($this) {
            self::Default => "#FFFFFF",
            self::Gray => "#EBECED",
            self::Red => "#FBE4E4",
            self::Pink => "#F4DFEB",
            self::Purple =>"#EAE4F2" ,
            self::Blue => "#DDEBF1",
            self::Green => "#DDEDEA",
            self::Yellow => "#FBF3DB",
            self::Orange => "#FAEBDD",
            self::Brown => "#E9E5E3",
        };
    }


}

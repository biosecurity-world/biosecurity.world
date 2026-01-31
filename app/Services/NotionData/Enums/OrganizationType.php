<?php

namespace App\Services\NotionData\Enums;

use Felix\PHPColor\Hsla;
use Illuminate\Support\Str;

enum OrganizationType: string
{
    case ForProfit = 'For-profit company';
    case ThinkTank = 'Think tank';
    case Government = 'Government';
    case Intergovernmental = 'Intergovernmental agency';
    case NationalNgo = 'National non-profit organization';
    case InternationalNgo = 'International non-profit organization';
    case Media = 'Media';
    case Research = 'Research institute / lab / network';

    public function slug(): string
    {
        return Str::slug($this->value);
    }

    public function color(): Hsla
    {
        return Hsla::fromHex(match ($this) {
            self::ForProfit => '#8b5cf6',
            self::ThinkTank => '#06b6d4',
            self::Government => '#f59e0b',
            self::Intergovernmental => '#ef4444',
            self::NationalNgo => '#10b981',
            self::InternationalNgo => '#3b82f6',
            self::Media => '#ec4899',
            self::Research => '#6366f1',
        });
    }

    public function darkColor(): Hsla
    {
        return $this->color()->darken(10);
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::ForProfit => 'For-profit company',
            self::ThinkTank => 'Think tank',
            self::Government => 'Government',
            self::Intergovernmental => 'Intergovernmental',
            self::NationalNgo => 'National NGO',
            self::InternationalNgo => 'International NGO',
            self::Media => 'Media',
            self::Research => 'Research institute',
        };
    }

    public static function fromString(string $value): ?self
    {
        return self::tryFrom($value);
    }

    public static function defaultSlug(): string
    {
        return 'unknown';
    }

    public static function defaultColor(): Hsla
    {
        return Hsla::fromHex('#6b7280');
    }

    public static function defaultDarkColor(): Hsla
    {
        return self::defaultColor()->darken(10);
    }

    public function order(): int
    {
        return match ($this) {
            self::Research => 0,
            self::InternationalNgo => 1,
            self::NationalNgo => 2,
            self::Government => 3,
            self::Intergovernmental => 4,
            self::ThinkTank => 5,
            self::ForProfit => 6,
            self::Media => 7,
        };
    }

    public static function defaultOrder(): int
    {
        return PHP_INT_MAX;
    }
}

<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Region: string implements HasLabel, HasColor
{
    case US = 'US';
    case EU = 'EU';
    case MENA = 'MENA';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::US => 'United States',
            self::EU => 'EU',
            self::MENA => 'Middle East and North Africa'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::US => Color::Blue,
            self::EU => Color::Green,
            self::MENA => Color::Indigo,
        };
    }

    // public function getIcon(): ?string
    // {
    //     return match ($this) {
    //         self::US => 'heroicon-o-document-text',
    //         self::EU => 'heroicon-o-eye',
    //         self::MENA => 'heroicon-o-archive-box',
    //     };
    // }
}

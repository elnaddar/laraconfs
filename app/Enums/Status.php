<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;

enum Status: string implements HasLabel, HasColor, HasIcon
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Archived => 'Archived'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => Color::Blue,
            self::Published => Color::Green,
            self::Archived => Color::Gray,
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-document-text',
            self::Published => 'heroicon-o-eye',
            self::Archived => 'heroicon-o-archive-box',
        };
    }

    public function getTab()
    {
        return fn(Builder $query) => $query->where('status', $this);
    }
}

<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Transmission: string implements HasLabel
{
    case Automatic = 'automatic';
    case Manual = 'manual';

    public function getLabel(): ?string
    {
         return match ($this) {
            self::Automatic => 'Automatik',
            self::Manual => 'Handschaltung',
        };
    }
}

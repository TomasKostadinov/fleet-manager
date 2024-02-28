<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FuelType: string implements HasLabel
{
    case Petrol = 'petrol';
    case Diesel = 'diesel';
    case Electric = 'electric';
    case Hybrid = 'hybrid';

    public function getLabel(): ?string
    {
         return match ($this) {
            self::Petrol => 'Benzin',
            self::Diesel => 'Diesel',
            self::Electric => 'Elektrisch',
            self::Hybrid => 'Hybrid',
        };
    }
}

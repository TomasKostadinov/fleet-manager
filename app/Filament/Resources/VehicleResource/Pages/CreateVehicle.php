<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }
}

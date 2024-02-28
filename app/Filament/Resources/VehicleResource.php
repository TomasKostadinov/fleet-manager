<?php

namespace App\Filament\Resources;

use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Fahrzeugdaten')
                    ->aside()
                    ->description('Grundlegende Informationen zum Fahrzeug.')
                    ->schema([
                        Forms\Components\TextInput::make('manufacturer')->required(),
                        Forms\Components\TextInput::make('model')->required(),
                        Forms\Components\TextInput::make('seats')->required(),
                        Forms\Components\TextInput::make('doors')->required(),
                        Forms\Components\TextInput::make('kw')->numeric()->required(),
                        Forms\Components\Select::make('transmission')
                            ->options(Transmission::class),
                        Forms\Components\Select::make('fuel_type')
                            ->options(FuelType::class),
                    ]),
                Section::make('Registrierung')
                    ->aside()
                    ->description('Informationen zur Registrierung und TÜV-Prüfung')
                    ->schema([
                        Forms\Components\TextInput::make('registration_plate')->required(),
                        Forms\Components\TextInput::make('chassis_number')->required(),
                        Forms\Components\Datepicker::make('registration_date')->required(),
                        Forms\Components\Datepicker::make('tuev_valid_until')->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_plate')
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold)
                    ->fontFamily(FontFamily::Mono),
                Tables\Columns\TextColumn::make('model'),
                Tables\Columns\TextColumn::make('manufacturer'),
                Tables\Columns\TextColumn::make('chassis_number'),
                Tables\Columns\TextColumn::make('fuel_type')->badge()
                    ->color(fn(FuelType $state): string => match ($state) {
                        FuelType::Petrol => 'info',
                        FuelType::Diesel => 'primary',
                        FuelType::Electric => 'success',
                        FuelType::Hybrid => 'success',
                    }),
                Tables\Columns\TextColumn::make('registration_date')->dateTime('d.m.Y'),
                Tables\Columns\TextColumn::make('tuev_valid_until')->dateTime('d.m.Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'view' => Pages\ViewVehicle::route('/{record}'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}

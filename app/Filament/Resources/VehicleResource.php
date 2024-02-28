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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $label = 'Fahrzeug';
    protected static ?string $pluralLabel = 'Fahrzeuge';
    public static function getGloballySearchableAttributes(): array
    {
        return ['model', 'manufacturer', 'registration_plate'];
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Fahrzeugdaten')
                    ->columns()
                    ->collapsible()
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
                    ])->collapsible()->persistCollapsed(),
                Section::make('Registrierung')
                    ->columns()
                    ->collapsible()
                    ->persistCollapsed()
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
                Tables\Columns\TextColumn::make('manufacturer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('model')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fuel_type')
                    ->sortable()
                    ->badge()
                    ->color(fn(FuelType $state): string => match ($state) {
                        FuelType::Petrol => 'info',
                        FuelType::Diesel => 'primary',
                        FuelType::Electric => 'success',
                        FuelType::Hybrid => 'success',
                    }),
                Tables\Columns\TextColumn::make('tuev_valid_until')
                    ->sortable()
                    ->dateTime('d.m.Y'),
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
            RelationManagers\VehicleBriefingRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return new HtmlString("<b>{$record->registration_plate}</b> ({$record->manufacturer} {$record->model})");
    }
}

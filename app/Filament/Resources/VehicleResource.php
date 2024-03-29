<?php

namespace App\Filament\Resources;

use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
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

    protected static ?string $navigationGroup = 'Flottenmanagement';

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
                    ->description('Grundlegende Informationen zum Fahrzeug.')
                    ->columns()
                    ->schema([
                        TextInput::make('manufacturer')
                            ->required()
                            ->label('Hersteller'),
                        TextInput::make('model')
                            ->required()
                            ->label('Modell'),
                        TextInput::make('seats')
                            ->required()
                            ->label('Sitzplätze'),
                        TextInput::make('doors')
                            ->required()
                            ->label('Türen'),
                        TextInput::make('kw')->numeric()
                            ->required()
                            ->label('Leistung (kW)'),
                        Forms\Components\Select::make('transmission')
                            ->label('Getriebe')
                            ->options(Transmission::class),
                        Forms\Components\Select::make('fuel_type')
                            ->label('Kraftstoff')
                            ->options(FuelType::class),
                    ])->collapsible()->persistCollapsed(),
                Section::make('Registrierung')
                    ->columns()
                    ->collapsible()
                    ->persistCollapsed()
                    ->description('Informationen zur Registrierung und TÜV-Prüfung')
                    ->schema([
                        TextInput::make('registration_plate')
                            ->required()
                            ->label('Kennzeichen'),
                        TextInput::make('chassis_number')
                            ->required()
                            ->label('Fahrgestellnummer'),
                        DatePicker::make('registration_date')
                            ->required()
                            ->label('Erstzulassung'),
                        Datepicker::make('tuev_valid_until')
                            ->required()
                            ->label('TÜV gültig bis'),
                    ]),
                Section::make('Dateien')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('document')
                            ->label('Dateien')
                            ->collection('documents')
                            ->visibility('private')
                            ->multiple()
                            ->removeUploadedFileButtonPosition('right')
                            ->uploadButtonPosition('left')
                            ->uploadProgressIndicatorPosition('left')
                            ->openable()
                            ->downloadable()
                            ->previewable()
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_plate')
                    ->label('Kennzeichen')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->weight(FontWeight::Bold)
                    ->fontFamily(FontFamily::Mono),
                Tables\Columns\TextColumn::make('manufacturer')
                    ->label('Hersteller')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Modell')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fuel_type')
                    ->label('Kraftstoff')
                    ->sortable()
                    ->badge()
                    ->color(fn(FuelType $state): string => match ($state) {
                        FuelType::Petrol => 'info',
                        FuelType::Diesel => 'primary',
                        FuelType::Electric => 'success',
                        FuelType::Hybrid => 'success',
                    }),
                Tables\Columns\TextColumn::make('tuev_valid_until')
                    ->label('TÜV gültig bis')
                    ->sortable()
                    ->dateTime('d.m.Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            RelationManagers\VehicleBriefingRelationManager::class,
            RelationManagers\OdometerReadingRelationManager::class
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

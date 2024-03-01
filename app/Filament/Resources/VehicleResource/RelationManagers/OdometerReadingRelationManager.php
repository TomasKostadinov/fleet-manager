<?php

namespace App\Filament\Resources\VehicleResource\RelationManagers;

use App\Models\OdometerReading;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class OdometerReadingRelationManager extends RelationManager
{
    protected static string $relationship = 'odometerReadings';
    protected static ?string $label = 'Kilometerstand';
    protected static ?string $title = 'Kilometerstände';
    protected static ?string $pluralLabel = 'Kilometerstände';

    public function form(Form $form): Form
    {
        $vehicle = Vehicle::findOrFail($form->getLivewire()->ownerRecord->id);

        $lastOdometerValue = $vehicle->last_odometer_reading
            ? $vehicle->last_odometer_reading->value
            : 0;
        return $form
            ->schema([
                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->step('1')
                    ->default($lastOdometerValue)
                    ->minValue($lastOdometerValue + 1)
                    ->required(),
                DatePicker::make('date')
                    ->label('Datum')
                    ->required()
                    ->native(false)
                    ->default('now'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                Tables\Columns\TextColumn::make('value')
                    ->numeric()
                    ->formatStateUsing(
                        fn (string $state): string => Number::format($state) . ' km')->label('Kilometerstand')->sortable()
                    /*->description(function (OdometerReading $record) {
                        if ($record->vehicle->first_odometer_reading->id === $record->id) {
                            return 'Erster Kilometerstand';
                        }
                        $differenceInDays = $record->date->diffInDays($record->vehicle->last_odometer_reading->date);
                        $differenceInKm = $record->value - $record->vehicle->last_odometer_reading->value;
                        return "+{$differenceInKm}km - {$differenceInDays} Tage";
                        })
                    */
                ->copyable()->copyableState(function (OdometerReading $record) {
                    return Number::format($record->value);
                })->sortable()->searchable(),
            ])
            ->filters([
                Filter::make('date')
                    ->label('Datum')
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

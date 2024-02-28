<?php

namespace App\Filament\Resources\VehicleResource\RelationManagers;

use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Components\Datepicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehicleBriefingRelationManager extends RelationManager
{
    protected static string $relationship = 'briefings';
    protected static ?string $label = 'Fahrzeugeinweisung';
    protected static ?string $title = 'Fahrzeugeinweisungen';
    protected static ?string $pluralLabel = 'Fahrzeugeinweisungen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('person_id')
                    ->options(Person::all()->pluck('full_name', 'id'))
                    ->searchable(),
                Select::make('issuer_id')
                    ->options(Person::all()->pluck('full_name', 'id'))
                    ->searchable(),
                Datepicker::make('issue_date')->required()
                    ->native(false)
                    ->default('now'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('person.full_name')
            ->columns([
                TextColumn::make('person.full_name')->label('Fahrer'),
                TextColumn::make('issuer.full_name')->label('Einweiser'),
                TextColumn::make('issue_date')->date('d.m.Y')->label('Datum'),
            ])
            ->filters([
                //
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

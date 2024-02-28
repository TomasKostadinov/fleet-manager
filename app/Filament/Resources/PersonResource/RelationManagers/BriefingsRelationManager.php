<?php

namespace App\Filament\Resources\PersonResource\RelationManagers;

use App\Models\Person;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BriefingsRelationManager extends RelationManager
{
    protected static string $relationship = 'briefings';
    protected static ?string $label = 'Fahrzeugeinweisung';
    protected static ?string $title = 'Fahrzeugeinweisungen';
    protected static ?string $pluralLabel = 'Fahrzeugeinweisungen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vehicle_id')
                    ->options(Vehicle::all()->pluck('display_name', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('issuer_id')
                    ->options(Person::all()->pluck('full_name', 'id'))
                    ->searchable(),
                Forms\Components\Datepicker::make('issue_date')->required()
                    ->native(false)
                    ->default('now'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('vehicle.display_name')
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.display_name')->label('Fahrzeug'),
                Tables\Columns\TextColumn::make('issuer.full_name')->label('Einweiser'),
                Tables\Columns\TextColumn::make('issue_date')->date('d.m.Y')->label('Datum'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}

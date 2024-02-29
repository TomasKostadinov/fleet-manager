<?php

namespace App\Filament\Resources\PersonResource\RelationManagers;

use App\Models\Person;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
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
                Select::make('vehicle_id')
                    ->options(Vehicle::all()->pluck('display_name', 'id'))
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
            ->recordTitleAttribute('vehicle.display_name')
            ->columns([
                TextColumn::make('vehicle.display_name')->label('Fahrzeug'),
                TextColumn::make('issuer.full_name')->label('Einweiser'),
                TextColumn::make('issue_date')->date('d.m.Y')->label('Datum'),
            ])
            ->filters([
                TrashedFilter::make()
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
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

<?php

namespace App\Filament\Resources;

use App\Enums\FuelType;
use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Components\Datepicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;


    protected static ?string $label = 'Person';
    protected static ?string $pluralLabel = 'Personen';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personendaten')
                    ->description('Grundlegende Informationen zum Person.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')->required(),
                        TextInput::make('last_name')->required(),
                        TextInput::make('email')->email(),
                        TextInput::make('phone'),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
                Section::make('Führerscheindaten')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columns(2)
                    ->description('Führerscheindaten und letzte Prüfung')
                    ->schema([
                        Datepicker::make('license_issue_date')->required()
                            ->native(false),
                        Datepicker::make('last_license_check_date')->required()
                            ->native(false)
                            ->default('now'),
                    ]),
                Section::make('Bemerkungen')
                    ->collapsible()
                    ->schema([
                        Forms\Components\RichEditor::make('notes')->default(null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('last_name')
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('email')
                    ->copyable(),
                Tables\Columns\TextColumn::make('last_license_check_date')->dateTime('d.m.Y'),
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
            RelationManagers\BriefingsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}

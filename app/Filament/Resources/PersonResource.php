<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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
                        Forms\Components\TextInput::make('first_name')->required(),
                        Forms\Components\TextInput::make('last_name')->required(),
                        Forms\Components\TextInput::make('email')->email(),
                        Forms\Components\TextInput::make('phone'),
                    ])->collapsible(),
                Section::make('Führerscheindaten')
                    ->collapsible()
                    ->columns(2)
                    ->description('Führerscheindaten und letzte Prüfung')
                    ->schema([
                        Forms\Components\Datepicker::make('license_issue_date')->required(),
                        Forms\Components\Datepicker::make('last_license_check_date')->required(),
                    ]),
                Section::make('Bemerkungen')
                    ->collapsible()
                    ->schema([
                        Forms\Components\RichEditor::make('notes'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            //
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

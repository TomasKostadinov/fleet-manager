<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;


    protected static ?string $label = 'Person';
    protected static ?string $pluralLabel = 'Personen';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Verwaltung';

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
                    ->columns()
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Vorname')
                            ->required(),
                        TextInput::make('last_name')
                            ->label('Nachname')
                            ->required(),
                        TextInput::make('email')
                            ->label('E-Mail')
                            ->email(),
                        TextInput::make('phone')
                            ->label('Telefon')
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
                Section::make('Führerscheindaten')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columns(2)
                    ->description('Führerscheindaten und letzte Prüfung')
                    ->schema([
                        DatePicker::make('license_issue_date')->required()
                            ->label('Ausstellungsdatum')
                            ->native(false),
                        DatePicker::make('last_license_check_date')->required()
                            ->label('Letzte Prüfung')
                            ->native(false)
                            ->default('now'),
                        Toggle::make('blue_light_rights')
                            ->label('Sonder- und Wegerechte')
                            ->onColor('blue')
                            ->offColor('black')
                    ]),
                Section::make('Bemerkungen')
                    ->collapsible()
                    ->schema([
                        Forms\Components\RichEditor::make('notes')
                            ->label('Notizen')
                            ->default(null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('last_name')
                    ->sortable()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->sortable()
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('email')
                    ->copyable(),
                Tables\Columns\TextColumn::make('last_license_check_date')
                    ->dateTime('d.m.Y'),
            ])
            ->filters([
                TernaryFilter::make('blue_light_rights')
                    ->label('Sonder- und Wegerechte')
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

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Filament\Resources\BillResource\RelationManagers;
use App\Models\Bill;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static ?string $label = 'Rechnung';
    protected static ?string $pluralLabel = 'Rechnungen';

    protected static ?string $navigationGroup = 'Flottenmanagement';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Rechnungsdaten')
                    ->description('Grundlegende Informationen zur Rechnung.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titel')
                            ->required(),
                        DatePicker::make('date')->required()
                            ->label('Rechnungsdatum')
                            ->default(now())
                            ->native(false),
                        TextInput::make('amount')
                            ->label('Betrag')
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('€')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->stripCharacters('.')
                            ->numeric() /*
                            ->mask(RawJs::make(
                                <<<'JS'
                                $money($input, ',', '.', 2, '€')
                                JS
                            ))
                            */
                            ->required(),
                        Toggle::make('paid')
                            ->label('Rechnung bezahlt')
                            ->onColor('green')
                            ->offColor('black'),
                        Select::make('vehicle_id')
                            ->label('Fahrzeug')
                            ->options(Vehicle::all()->pluck('display_name', 'id'))
                            ->searchable(),
                    ])
                    ->persistCollapsed(),
                Section::make('Bemerkungen & Dateien')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Notizen')
                            ->default(null),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Titel')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Betrag')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle.display_name')
                    ->label('Fahrzeug')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Rechnungsdatum')
                    ->date('d.m.Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('paid')
                    ->label('Bezahlt')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                // filter for paid
                Tables\Filters\SelectFilter::make('paid')
                    ->label('Bezahlt')
                    ->options([
                        'true' => 'Ja',
                        'false' => 'Nein',
                    ]),
                // filter for vehicle
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Fahrzeug')
                    ->options(
                        Vehicle::all()->pluck('display_name', 'id')->toArray()
                    ),
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
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }

    protected static ?string $navigationBadgeTooltip = 'Anzahl der offenen Rechnungen';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()->where('paid', false)->count();
    }
}

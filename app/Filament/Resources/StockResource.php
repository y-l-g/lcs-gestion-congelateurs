<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Produit;
use App\Models\Stock;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('produit_id')
                    ->relationship('produit', 'nom')

                    ->searchable(isIndividual: true)
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->editOptionForm([
                        Forms\Components\TextInput::make('nom')
                            ->required(),
                    ]),
                Forms\Components\Select::make('congelateur')
                    ->options(['Grand' => 'Grand', 'Petit' => 'Petit', 'Menimur' => 'Menimur'])
                    ->default('Grand')
                    ->required()
                    ->searchable(isIndividual: true),
                Forms\Components\TextInput::make('poids')
                    ->numeric()
                    ->prefix('Poids en g')
                    ->suffix('g'),
                Forms\Components\Select::make('etage')
                    ->options([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7])
                    ->default(1)
                    ->searchable(isIndividual: true),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(100)
            ->columns([
                Tables\Columns\TextColumn::make('produit.nom')
                    ->label('Produit')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('congelateur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('poids')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => $state . ' g'),
                Tables\Columns\TextColumn::make('etage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label('Ajouté le')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Supprimé le')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\TrashedFilter::make(),
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
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}




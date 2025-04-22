<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Produit;
use App\Models\Stock;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ReplicateAction;


class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationLabel = 'Stocks';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                Fieldset::make("C'est quoi ?")
                    ->columns(6)
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\Select::make('produit_id')
                            ->columnSpan(3)
                            ->relationship('produit', 'nom')
                            ->placeholder("produit")
                            ->searchable()
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
                        Forms\Components\TextInput::make('poids')
                            ->columnSpan(2)
                            ->numeric()
                            ->suffix('g'),
                        Toggle::make('fruit')
                            ->columnSpan(1)
                            ->inline(false)

                        ,
                    ]),

                Fieldset::make("C'est où ?")
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\Select::make('congelateur')
                            ->options(['Grand' => 'Grand', 'Petit' => 'Petit', 'Menimur' => 'Menimur'])
                            ->default('Grand')
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('etage')
                            ->options([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7])
                            ->default(1)
                            ->searchable(),
                    ]),

                Fieldset::make("C'est quand ?")
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\DatePicker::make('date_entree')
                            ->date()
                            ->default('now'),
                        Forms\Components\DatePicker::make('date_sortie')
                            ->date(),
                    ]),
                Fieldset::make("Y'en a combien ?")
                    ->hiddenOn('edit')
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('quant')
                            ->numeric()
                            ->default(1)
                            ->label("Quantité"),
                    ]),



            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('produit.nom')
                    ->label('Produit')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('poids')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => $state . ' g'),
                Tables\Columns\TextColumn::make('congelateur')
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('etage')
                    ->options([
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                        '6' => '6',
                        '7' => '7',
                    ])
                    ->sortable()
                    ->width('1%')
                    ->disabled(!auth()->user()->is_admin),
                ToggleColumn::make('fruit')
                    ->disabled(!auth()->user()->is_admin),
                Tables\Columns\TextColumn::make('date_entree')
                    ->date()
                    ->label("Date d'entrée")
                    ->sortable()
                    ->searchable()
                    ->disabled(!auth()->user()->is_admin),
                TextInputColumn::make('date_sortie')->type('date')->label('Date de sortie')->sortable()->searchable()->disabled(!auth()->user()->is_admin),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->label("Modifié le")
                    ->sortable()
                    ->searchable()
                    ->disabled(!auth()->user()->is_admin),
            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(''),
                ReplicateAction::make()
                    ->requiresConfirmation(false)
                    ->label('')
                    ->hidden(!auth()->user()->is_admin),
                Tables\Actions\DeleteAction::make()
                    ->label(''),

            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->hidden(!auth()->user()->is_admin),
            ])
            ->filters([
                TernaryFilter::make('date_sortie')
                    ->label('Produits sortis')
                    ->placeholder('Afficher tous les produits')
                    ->trueLabel('Ne pas afficher les produits sortis')
                    ->falseLabel('Afficher seulement les produits sortis')
                    ->queries(
                        true: fn(Builder $query): Builder => $query->whereNull('date_sortie'),
                        false: fn(Builder $query): Builder => $query->whereNotNull('date_sortie'),
                        blank: fn(Builder $query) => $query
                    )
                    ->default(),
                TernaryFilter::make('date_entrée')
                    ->label("Date d'entrée")
                    ->placeholder('Depuis toujours')
                    ->trueLabel('Plus de 6 mois')
                    ->falseLabel("Plus d'un an")
                    ->queries(
                        true: fn(Builder $query): Builder => $query
                            ->whereDate('date_entree', '<', Carbon::now()->subMonths(6))
                            ->orWhereNull('date_entree'),
                        false: fn(Builder $query): Builder => $query
                            ->whereDate('date_entree', '<', Carbon::now()->subYear())
                            ->orWhereNull('date_entree'),
                        blank: fn(Builder $query) => $query
                    ),
                Filter::make('fruit')
                    ->toggle()
                    ->label("Seulement les fruits")
                    ->query(fn(Builder $query): Builder => $query
                        ->where('fruit', "=", true)),
                SelectFilter::make('congelateur')
                    ->multiple()
                    ->label('Congelateurs')
                    ->options([
                        'Petit' => 'Petit',
                        'Grand' => 'Grand',
                        'Menimur' => 'Menimur',
                    ]),
                SelectFilter::make('produit_id')
                    ->multiple()
                    ->relationship('produit', 'nom')
                    ->preload()
                    ->label('Produits')
                    ->searchable()
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
}




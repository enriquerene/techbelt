<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceResource\Pages;
use App\Models\ResourceItem;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResourceResource extends Resource
{
    protected static ?string $model = ResourceItem::class;

    protected static ?string $modelLabel = 'Recurso';

    protected static ?string $pluralModelLabel = 'Recursos';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 30;

    public static function getNavigationLabel(): string
    {
        return 'Recursos';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Recurso')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->label('Nome'),
                        Forms\Components\Select::make('category')
                            ->required()
                            ->options(ResourceItem::categories())
                            ->native(false)
                            ->label('Categoria'),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->label('Descrição'),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(1)
                            ->label('Quantidade'),
                        Forms\Components\TextInput::make('unit_cost')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('R$')
                            ->label('Custo Unitário'),
                        Forms\Components\TextInput::make('total_cost')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('R$')
                            ->label('Custo Total')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Calculado automaticamente: quantidade × custo unitário'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Datas e Status')
                    ->schema([
                        Forms\Components\DatePicker::make('purchase_date')
                            ->required()
                            ->default(now())
                            ->label('Data de Compra'),
                        Forms\Components\DatePicker::make('next_maintenance_date')
                            ->nullable()
                            ->label('Próxima Data de Manutenção'),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options(ResourceItem::statuses())
                            ->native(false)
                            ->default('available')
                            ->label('Status'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Responsabilidade')
                    ->schema([
                        Forms\Components\Select::make('responsible_user_id')
                            ->label('Pessoa Responsável')
                            ->relationship('responsibleUser', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'first_aid' => 'danger',
                        'maintenance' => 'warning',
                        'marketing' => 'info',
                        'equipment' => 'success',
                        'supplies' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ResourceItem::categories()[$state] ?? ucfirst(str_replace('_', ' ', $state)))
                    ->label('Categoria'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->label('Quantidade'),
                Tables\Columns\TextColumn::make('unit_cost')
                    ->money('BRL')
                    ->sortable()
                    ->label('Custo Unitário'),
                Tables\Columns\TextColumn::make('total_cost')
                    ->money('BRL')
                    ->sortable()
                    ->label('Custo Total'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'in_use' => 'info',
                        'maintenance' => 'warning',
                        'depleted' => 'danger',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ResourceItem::statuses()[$state] ?? ucfirst(str_replace('_', ' ', $state)))
                    ->label('Status'),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date()
                    ->sortable()
                    ->label('Data de Compra'),
                Tables\Columns\TextColumn::make('next_maintenance_date')
                    ->date()
                    ->sortable()
                    ->placeholder('Não agendada')
                    ->label('Próxima Manutenção'),
                Tables\Columns\TextColumn::make('responsibleUser.name')
                    ->label('Responsável')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(ResourceItem::categories())
                    ->label('Categoria'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(ResourceItem::statuses())
                    ->label('Status'),
                Tables\Filters\Filter::make('needs_maintenance')
                    ->label('Precisa de Manutenção')
                    ->query(fn (Builder $query): Builder => $query->where('next_maintenance_date', '<=', now()->addDays(30))),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResources::route('/'),
            'create' => Pages\CreateResource::route('/create'),
            'edit' => Pages\EditResource::route('/{record}/edit'),
        ];
    }
}

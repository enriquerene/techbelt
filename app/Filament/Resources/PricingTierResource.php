<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricingTierResource\Pages;
use App\Models\PricingTier;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form as FilamentForm;
use Filament\Tables\Table as FilamentTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PricingTierResource extends Resource
{
    protected static ?string $model = PricingTier::class;

    protected static ?string $modelLabel = 'Plano';

    protected static ?string $pluralModelLabel = 'Planos';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getNavigationLabel(): string
    {
        return 'Planos de Preços';
    }

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form->schema([
            Forms\Components\Section::make('Detalhes')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label('Nome'),
                    Forms\Components\MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->label('Descrição'),
                ]),

            Forms\Components\Section::make('Financeiro')
                ->schema([
                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->required()
                        ->prefix('R$')
                        ->rules(['min:0'])
                        ->label('Preço'),
                    Forms\Components\TextInput::make('comparative_price')
                        ->label('Preço Comparativo (Riscado)')
                        ->numeric()
                        ->prefix('R$')
                        ->rules(['min:0', 'nullable'])
                        ->helperText('Preço original para mostrar riscado em promoções'),
                    Forms\Components\Select::make('billing_period')
                        ->options([
                            'monthly' => 'Mensal',
                            'quarterly' => 'Trimestral',
                            'yearly' => 'Anual',
                        ])
                        ->default('monthly')
                        ->required()
                        ->label('Período de Cobrança'),
                ])->columns(3),

            Forms\Components\Section::make('Limites')
                ->schema([
                    Forms\Components\Radio::make('frequency_type')
                        ->options([
                            'unlimited' => 'Ilimitado',
                            'fixed' => 'Quantidade Fixa',
                        ])
                        ->default('unlimited')
                        ->required()
                        ->live()
                        ->label('Tipo de Frequência'),
                    Forms\Components\TextInput::make('class_cap')
                        ->label('Limite de Aulas (por semana)')
                        ->numeric()
                        ->minValue(1)
                        ->required(fn (Forms\Get $get): bool => $get('frequency_type') === 'fixed')
                        ->hidden(fn (Forms\Get $get): bool => $get('frequency_type') !== 'fixed'),
                    Forms\Components\TextInput::make('class_count')
                        ->label('Quantidade Padrão de Aulas')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Escopo')
                ->schema([
                    Forms\Components\CheckboxList::make('modalities')
                        ->relationship('modalities', 'name')
                        ->searchable()
                        ->columns(2)
                        ->helperText('Selecione quais modalidades este plano se aplica')
                        ->label('Modalidades'),
                ]),

            Forms\Components\Section::make('Informações Adicionais')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Ativo')
                        ->default(true)
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull()
                        ->label('Observações'),
                ]),
        ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('price')
                    ->money('BRL')
                    ->sortable()
                    ->label('Preço'),
                Tables\Columns\TextColumn::make('billing_period')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'monthly' => 'info',
                        'quarterly' => 'warning',
                        'yearly' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly' => 'Mensal',
                        'quarterly' => 'Trimestral',
                        'yearly' => 'Anual',
                        default => ucfirst($state),
                    })
                    ->sortable()
                    ->label('Período de Cobrança'),
                Tables\Columns\TextColumn::make('class_count')
                    ->label('Créditos')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Ativo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->counts('subscriptions')
                    ->label('Assinaturas Ativas')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Ativo'),
                Tables\Filters\SelectFilter::make('billing_period')
                    ->options([
                        'monthly' => 'Mensal',
                        'quarterly' => 'Trimestral',
                        'yearly' => 'Anual',
                    ])
                    ->label('Período de Cobrança'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (PricingTier $record) => $record->hasActiveSubscriptions()),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\Action::make('archive')
                    ->label('Arquivar')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Arquivar Plano de Preço')
                    ->modalDescription('Isso marcará o plano de preço como inativo.')
                    ->action(function (PricingTier $record) {
                        $record->update(['is_active' => false]);
                    })
                    ->hidden(fn (PricingTier $record) => !$record->is_active),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])->headerActions([
            Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListPricingTiers::route('/'),
            'create' => Pages\CreatePricingTier::route('/create'),
            'edit' => Pages\EditPricingTier::route('/{record}/edit'),
        ];
    }
}

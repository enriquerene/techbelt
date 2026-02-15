<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table as FilamentTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $modelLabel = 'Despesa';

    protected static ?string $pluralModelLabel = 'Despesas';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static ?string $navigationGroup = 'Financeiro';

    public static function getNavigationLabel(): string
    {
        return 'Despesas';
    }

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form->schema([
            Forms\Components\Section::make('Detalhes da Despesa')
                ->schema([
                    Forms\Components\TextInput::make('description')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->label('Descrição'),
                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->required()
                        ->prefix('R$')
                        ->rules(['min:0'])
                        ->label('Valor'),
                    Forms\Components\Select::make('category')
                        ->options(Expense::categories())
                        ->required()
                        ->default(Expense::CATEGORY_OTHER)
                        ->label('Categoria'),
                    Forms\Components\DatePicker::make('date')
                        ->required()
                        ->default(now())
                        ->label('Data'),
                    Forms\Components\Select::make('payment_method')
                        ->options(Expense::paymentMethods())
                        ->required()
                        ->default(Expense::PAYMENT_METHOD_CASH)
                        ->label('Método de Pagamento'),
                ])->columns(2),

            Forms\Components\Section::make('Informações Adicionais')
                ->schema([
                    Forms\Components\Select::make('staff_id')
                        ->label('Membro da Equipe (se pagamento de funcionário)')
                        ->options(function () {
                            return \App\Models\User::whereJsonContains('role', 'staff')
                                ->orWhereJsonContains('role', 'instructor')
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->nullable(),
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
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->label('Descrição'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('BRL')
                    ->sortable()
                    ->label('Valor'),
                Tables\Columns\BadgeColumn::make('category')
                    ->formatStateUsing(fn (string $state): string => Expense::categories()[$state] ?? $state)
                    ->colors([
                        'warning' => Expense::CATEGORY_STAFF_PAYMENT,
                        'info' => Expense::CATEGORY_MAINTENANCE,
                        'success' => Expense::CATEGORY_MARKETING,
                        'gray' => Expense::CATEGORY_OTHER,
                    ])
                    ->sortable()
                    ->label('Categoria'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->label('Data'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Dinheiro',
                        'bank_transfer' => 'Transferência Bancária',
                        'credit_card' => 'Cartão de Crédito',
                        'pix' => 'PIX',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->sortable()
                    ->label('Método de Pagamento'),
                Tables\Columns\TextColumn::make('staff.name')
                    ->label('Membro da Equipe')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(Expense::categories())
                    ->label('Categoria'),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(Expense::paymentMethods())
                    ->label('Método de Pagamento'),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}

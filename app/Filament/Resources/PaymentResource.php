<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Enrollment;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form as FilamentForm;
use Filament\Tables\Table as FilamentTable;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $modelLabel = 'Pagamento';

    protected static ?string $pluralModelLabel = 'Pagamentos';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Pagamentos';

    protected static ?string $navigationGroup = 'Financeiro';

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationLabel(): string
    {
        return 'Pagamentos';
    }

    public static function form(FilamentForm $form): FilamentForm
    {
        return $form->schema([
            Forms\Components\Section::make('Detalhes do Pagamento')
                ->schema([
                    Forms\Components\Select::make('enrollment_id')
                        ->relationship('enrollment', 'id')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Matrícula')
                        ->getOptionLabelFromRecordUsing(fn (Enrollment $record) => "Matrícula #{$record->id}"),
                    
                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->prefix('R$')
                        ->required()
                        ->label('Valor'),
                    
                    Forms\Components\Select::make('payment_method')
                        ->options(Payment::paymentMethodOptions())
                        ->required()
                        ->label('Método de Pagamento'),
                    
                    Forms\Components\Select::make('status')
                        ->options(Payment::statusOptions())
                        ->default(Payment::STATUS_COMPLETED)
                        ->required()
                        ->label('Status'),
                    
                    Forms\Components\DateTimePicker::make('paid_at')
                        ->label('Data do Pagamento')
                        ->default(now()),
                    
                    Forms\Components\Textarea::make('notes')
                        ->label('Observações')
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('enrollment.id')
                    ->label('Matrícula ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método de Pagamento')
                    ->formatStateUsing(fn (string $state): string => Payment::paymentMethodOptions()[$state] ?? $state)
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => Payment::STATUS_PENDING,
                        'success' => Payment::STATUS_COMPLETED,
                        'danger' => Payment::STATUS_FAILED,
                        'gray' => Payment::STATUS_REFUNDED,
                    ])
                    ->formatStateUsing(fn (string $state): string => Payment::statusOptions()[$state] ?? $state)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Data do Pagamento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Payment::statusOptions())
                    ->label('Status'),
                
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(Payment::paymentMethodOptions())
                    ->label('Método de Pagamento'),
                
                Tables\Filters\Filter::make('paid_at')
                    ->form([
                        Forms\Components\DatePicker::make('paid_from')
                            ->label('De'),
                        Forms\Components\DatePicker::make('paid_until')
                            ->label('Até'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['paid_from'], fn ($query, $date) => $query->whereDate('paid_at', '>=', $date))
                            ->when($data['paid_until'], fn ($query, $date) => $query->whereDate('paid_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Detalhes do Pagamento')
                ->schema([
                    Infolists\Components\TextEntry::make('enrollment.user.name')
                        ->label('Aluno'),
                    
                    Infolists\Components\TextEntry::make('enrollment.pricingTier.name')
                        ->label('Plano'),
                    
                    Infolists\Components\TextEntry::make('amount')
                        ->label('Valor')
                        ->money('BRL'),
                    
                    Infolists\Components\TextEntry::make('payment_method')
                        ->label('Método de Pagamento')
                        ->formatStateUsing(fn (string $state): string => Payment::paymentMethodOptions()[$state] ?? $state),
                    
                    Infolists\Components\TextEntry::make('status')
                        ->label('Status')
                        ->formatStateUsing(fn (string $state): string => Payment::statusOptions()[$state] ?? $state),
                    
                    Infolists\Components\TextEntry::make('paid_at')
                        ->label('Data do Pagamento')
                        ->dateTime('d/m/Y H:i'),
                    
                    Infolists\Components\TextEntry::make('notes')
                        ->label('Observações')
                        ->columnSpanFull(),
                    
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Registrado em')
                        ->dateTime('d/m/Y H:i'),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            // No relations needed for payments
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
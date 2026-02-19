<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentPaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->with(['enrollment.user', 'enrollment.pricingTier'])
                    ->where('status', 'completed')
                    ->latest('paid_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('enrollment.user.name')
                    ->label('Aluno')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('enrollment.pricingTier.name')
                    ->label('Plano')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método de Pagamento')
                    ->formatStateUsing(fn (string $state): string => $this->formatPaymentMethod($state))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Data do Pagamento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ])
                    ->formatStateUsing(fn (string $state): string => $this->translateStatus($state)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Payment $record): string => route('filament.admin.resources.payments.view', $record)),
            ])
            ->emptyStateHeading('Nenhum pagamento recente')
            ->emptyStateDescription('Quando houver pagamentos, eles aparecerão aqui.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }
    
    private function formatPaymentMethod(string $method): string
    {
        $methods = [
            'credit_card' => 'Cartão de Crédito',
            'debit_card' => 'Cartão de Débito',
            'pix' => 'PIX',
            'bank_slip' => 'Boleto',
            'cash' => 'Dinheiro',
            'transfer' => 'Transferência',
        ];
        
        return $methods[$method] ?? ucfirst(str_replace('_', ' ', $method));
    }
    
    private function translateStatus(string $status): string
    {
        $translations = [
            'pending' => 'Pendente',
            'completed' => 'Concluído',
            'failed' => 'Falhou',
            'refunded' => 'Reembolsado',
            'cancelled' => 'Cancelado',
        ];
        
        return $translations[$status] ?? ucfirst($status);
    }
}
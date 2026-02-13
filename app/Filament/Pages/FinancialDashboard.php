<?php

namespace App\Filament\Pages;

use App\Models\Enrollment;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class FinancialDashboard extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $title = 'Pagamentos';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Pagamentos';

    protected static ?string $navigationGroup = 'Financeiro';

    protected static string $view = 'filament.pages.financial-dashboard';

    public function table(Table $table): Table
    {
        return $table
            ->query(Enrollment::query()->with(['user', 'pricingTier']))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pricingTier.name')
                    ->label('Plan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_billing_date')
                    ->label('Next Billing')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'cancelled',
                        'warning' => 'overdue',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enrolled On')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'cancelled' => 'Cancelled',
                        'overdue' => 'Overdue',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'credit_card' => 'Credit Card',
                        'bank_transfer' => 'Bank Transfer',
                        'pix' => 'PIX',
                        'cash' => 'Cash',
                    ]),
            ])
            ->actions([
                // View action could be added here
            ])
            ->bulkActions([]);
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}

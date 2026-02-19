<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Painel de Controle';
    
    protected static ?string $title = 'Painel de Controle';
    
    public function getColumns(): int|string|array
    {
        return 2; // Customize grid columns
    }
    
    public function getWidgets(): array
    {
        return [
            // Statistics overview (4 cards in a row) - appears at top
            \App\Filament\Widgets\StatsOverviewWidget::class,
            
            // Cash flow chart (full width)
            \App\Filament\Widgets\MonthlyCashFlowChart::class,
            
            // Recent payments table (full width)
            \App\Filament\Widgets\RecentPaymentsWidget::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            // Add footer widgets if needed
        ];
    }
}

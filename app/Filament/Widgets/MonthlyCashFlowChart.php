<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyCashFlowChart extends ChartWidget
{
    protected static ?string $heading = 'Fluxo de Caixa Mensal';

    protected static ?int $sort = 2;
    
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $daysInMonth = Carbon::now()->daysInMonth;
        $today = Carbon::now()->day;

        // Initialize arrays for each day of the month
        $entradas = array_fill(1, $daysInMonth, 0);
        $saidas = array_fill(1, $daysInMonth, 0);
        $labels = [];
        
        // Get payments for current month (entradas)
        $payments = Payment::whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->where('status', Payment::STATUS_COMPLETED)
            ->select(
                DB::raw('DAY(paid_at) as day'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('DAY(paid_at)'))
            ->get()
            ->keyBy('day');
        
        // Get expenses for current month (saídas)
        $expenses = Expense::whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->select(
                DB::raw('DAY(date) as day'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('DAY(date)'))
            ->get()
            ->keyBy('day');
        
        // Fill the arrays with real data
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $labels[] = $day . '/' . $currentMonth;
            
            if ($payments->has($day)) {
                $entradas[$day] = (float) $payments[$day]->total;
            }
            
            if ($expenses->has($day)) {
                $saidas[$day] = (float) $expenses[$day]->total;
            }
        }
        
        // Calculate accumulated balance
        $balanco = [];
        $acumulado = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $acumulado += ($entradas[$day] - $saidas[$day]);
            $balanco[] = $acumulado;
        }
        
        // Convert indexed arrays to sequential arrays (starting from 0)
        $entradas = array_values(array_slice($entradas, 0, $daysInMonth));
        $saidas = array_values(array_slice($saidas, 0, $daysInMonth));
        
        // Only show up to today
        $labels = array_slice($labels, 0, $today);
        $entradas = array_slice($entradas, 0, $today);
        $saidas = array_slice($saidas, 0, $today);
        $balanco = array_slice($balanco, 0, $today);

        return [
            'datasets' => [
                [
                    'label' => 'Entradas (Pagamentos)',
                    'data' => $entradas,
                    'backgroundColor' => '#10B981', // green-500
                    'borderColor' => '#10B981',
                    'type' => 'bar',
                    'order' => 2,
                ],
                [
                    'label' => 'Saídas (Despesas)',
                    'data' => $saidas,
                    'backgroundColor' => '#EF4444', // red-500
                    'borderColor' => '#EF4444',
                    'type' => 'bar',
                    'order' => 1,
                ],
                [
                    'label' => 'Balanço Acumulado',
                    'data' => $balanco,
                    'backgroundColor' => '#3B82F6', // blue-500
                    'borderColor' => '#3B82F6',
                    'type' => 'line',
                    'order' => 0,
                    'fill' => false,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Valor (R$)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Dia do Mês',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
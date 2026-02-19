<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Total students
        $totalStudents = Student::count();
        
        // Monthly revenue (payments completed this month)
        $monthlyRevenue = Payment::whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->where('status', 'completed')
            ->sum('amount');
        
        // Monthly expenses
        $monthlyExpenses = Expense::whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->sum('amount');
        
        // Active enrollments
        $activeEnrollments = Enrollment::where('status', 'active')->count();
        
        // Monthly profit
        $monthlyProfit = $monthlyRevenue - $monthlyExpenses;
        
        return [
            Stat::make('Total de Alunos', $totalStudents)
                ->description('Alunos cadastrados no sistema')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart($this->getStudentGrowthChart()),
                
            Stat::make('Receita Mensal', 'R$ ' . number_format($monthlyRevenue, 2, ',', '.'))
                ->description('Receita deste mês')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->chart($this->getRevenueChart()),
                
            Stat::make('Lucro Mensal', 'R$ ' . number_format($monthlyProfit, 2, ',', '.'))
                ->description('Lucro após despesas')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($monthlyProfit >= 0 ? 'success' : 'danger')
                ->chart($this->getProfitChart()),
                
            Stat::make('Matrículas Ativas', $activeEnrollments)
                ->description('Matrículas em andamento')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('info')
                ->chart($this->getEnrollmentChart()),
        ];
    }
    
    private function getStudentGrowthChart(): array
    {
        // Get student count for last 6 months
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Student::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $data[] = $count;
        }
        
        return $data;
    }
    
    private function getRevenueChart(): array
    {
        // Get revenue for last 6 months
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Payment::whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->where('status', 'completed')
                ->sum('amount');
            $data[] = $revenue / 100; // Scale down for chart
        }
        
        return $data;
    }
    
    private function getProfitChart(): array
    {
        // Get profit for last 6 months
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Payment::whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->where('status', 'completed')
                ->sum('amount');
            $expenses = Expense::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
            $profit = $revenue - $expenses;
            $data[] = $profit / 100; // Scale down for chart
        }
        
        return $data;
    }
    
    private function getEnrollmentChart(): array
    {
        // Get enrollment count for last 6 months
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Enrollment::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'active')
                ->count();
            $data[] = $count;
        }
        
        return $data;
    }
}
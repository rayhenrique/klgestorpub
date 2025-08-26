<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Define o período com base no parâmetro da URL
        $period = $request->get('period', 'month');
        
        // Define as datas de início e fim com base no período
        switch ($period) {
            case 'quarter':
                $startDate = Carbon::now()->startOfQuarter();
                $endDate = Carbon::now()->endOfQuarter();
                $previousStart = Carbon::now()->subQuarter()->startOfQuarter();
                $previousEnd = Carbon::now()->subQuarter()->endOfQuarter();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $previousStart = Carbon::now()->subYear()->startOfYear();
                $previousEnd = Carbon::now()->subYear()->endOfYear();
                break;
            default: // month
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $previousStart = Carbon::now()->subMonth()->startOfMonth();
                $previousEnd = Carbon::now()->subMonth()->endOfMonth();
                break;
        }

        // Calcula totais do período atual
        $currentRevenues = Revenue::whereBetween('date', [$startDate, $endDate])->sum('amount');
        $currentExpenses = Expense::whereBetween('date', [$startDate, $endDate])->sum('amount');
        $balance = $currentRevenues - $currentExpenses;

        // Calcula totais do período anterior
        $previousRevenues = Revenue::whereBetween('date', [$previousStart, $previousEnd])->sum('amount');
        $previousExpenses = Expense::whereBetween('date', [$previousStart, $previousEnd])->sum('amount');

        // Calcula variação percentual
        $revenueChange = $previousRevenues > 0 ? 
            (($currentRevenues - $previousRevenues) / $previousRevenues) * 100 : 0;
        $expenseChange = $previousExpenses > 0 ? 
            (($currentExpenses - $previousExpenses) / $previousExpenses) * 100 : 0;

        // Dados para o gráfico de linha (últimos 6 períodos)
        $monthlyData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = match($period) {
                'quarter' => Carbon::now()->subQuarters($i),
                'year' => Carbon::now()->subYears($i),
                default => Carbon::now()->subMonths($i)
            };

            $periodStart = match($period) {
                'quarter' => $date->copy()->startOfQuarter(),
                'year' => $date->copy()->startOfYear(),
                default => $date->copy()->startOfMonth()
            };

            $periodEnd = match($period) {
                'quarter' => $date->copy()->endOfQuarter(),
                'year' => $date->copy()->endOfYear(),
                default => $date->copy()->endOfMonth()
            };

            $periodRevenues = Revenue::whereBetween('date', [$periodStart, $periodEnd])->sum('amount');
            $periodExpenses = Expense::whereBetween('date', [$periodStart, $periodEnd])->sum('amount');
            
            $monthlyData->push([
                'month' => match($period) {
                    'quarter' => $date->quarter . 'º Trim/' . $date->year,
                    'year' => $date->year,
                    default => $date->format('M/Y')
                },
                'revenues' => $periodRevenues,
                'expenses' => $periodExpenses
            ]);
        }

        // Dados para o gráfico de categorias (top 5 categorias de despesas)
        $expensesByCategory = Expense::select('categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'expenses.fonte_id', '=', 'categories.id')
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Últimas transações
        $latestRevenues = Revenue::select(
                'date',
                'description',
                DB::raw("'Receita' as type"),
                'amount',
                DB::raw("'success' as badge_color")
            )->latest('date')->limit(5);

        $latestTransactions = Expense::select(
                'date',
                'description',
                DB::raw("'Despesa' as type"),
                'amount',
                DB::raw("'danger' as badge_color")
            )
            ->union($latestRevenues)
            ->latest('date')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'currentRevenues',
            'currentExpenses',
            'balance',
            'revenueChange',
            'expenseChange',
            'monthlyData',
            'expensesByCategory',
            'latestTransactions',
            'period'
        ));
    }
} 
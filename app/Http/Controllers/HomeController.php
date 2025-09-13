<?php

namespace App\Http\Controllers;

use App\Models\Revenue;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Dados do mês atual
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Receitas e Despesas do Mês Atual
        $monthlyRevenue = Revenue::whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->sum('amount');

        $monthlyExpense = Expense::whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->sum('amount');

        // Receitas e Despesas do Mês Anterior
        $lastMonthRevenue = Revenue::whereYear('date', $lastMonth->year)
            ->whereMonth('date', $lastMonth->month)
            ->sum('amount');

        $lastMonthExpense = Expense::whereYear('date', $lastMonth->year)
            ->whereMonth('date', $lastMonth->month)
            ->sum('amount');

        // Cálculo do crescimento
        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        $expenseGrowth = $lastMonthExpense > 0
            ? round((($monthlyExpense - $lastMonthExpense) / $lastMonthExpense) * 100, 1)
            : 0;

        $currentBalance = $monthlyRevenue - $monthlyExpense;
        $lastBalance = $lastMonthRevenue - $lastMonthExpense;
        $balanceGrowth = $lastBalance != 0
            ? round((($currentBalance - $lastBalance) / abs($lastBalance)) * 100, 1)
            : 0;

        // Total de Classificações
        $totalClassifications = Category::count();

        // Dados para o Gráfico de Balanço (últimos 6 meses)
        $balanceData = $this->getBalanceChartData();

        // Dados para o Gráfico de Classificações
        $classificationData = $this->getClassificationChartData();

        // Últimas Transações
        $recentRevenues = Revenue::with(['fonte', 'acao'])
            ->latest('date')
            ->take(3)
            ->get()
            ->map(function($revenue) {
                return (object) [
                    'date' => $revenue->date,
                    'description' => $revenue->description,
                    'amount' => $revenue->amount,
                    'type' => 'revenue',
                    'category' => $revenue->acao ?? $revenue->fonte
                ];
            });

        $recentExpenses = Expense::with(['fonte', 'acao'])
            ->latest('date')
            ->take(3)
            ->get()
            ->map(function($expense) {
                return (object) [
                    'date' => $expense->date,
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'type' => 'expense',
                    'category' => $expense->acao ?? $expense->fonte
                ];
            });

        $recentTransactions = $recentRevenues->merge($recentExpenses)
            ->sortByDesc('date')
            ->take(5);

        return view('home', compact(
            'monthlyRevenue',
            'monthlyExpense',
            'revenueGrowth',
            'expenseGrowth',
            'balanceGrowth',
            'totalClassifications',
            'balanceData',
            'classificationData',
            'recentTransactions'
        ));
    }

    /**
     * Obtém dados para o gráfico de balanço dos últimos 6 meses
     */
    private function getBalanceChartData()
    {
        $months = collect([]);
        $revenues = collect([]);
        $expenses = collect([]);

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('M/Y'));

            $monthRevenue = Revenue::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');
            $revenues->push($monthRevenue);

            $monthExpense = Expense::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');
            $expenses->push($monthExpense);
        }

        return [
            'labels' => $months,
            'revenues' => $revenues,
            'expenses' => $expenses
        ];
    }

    /**
     * Obtém dados para o gráfico de classificações de despesas
     */
    private function getClassificationChartData()
    {
        $currentMonth = Carbon::now();

        $classifications = Expense::select('categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'expenses.fonte_id', '=', 'categories.id')
            ->whereYear('expenses.date', $currentMonth->year)
            ->whereMonth('expenses.date', $currentMonth->month)
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $classifications->pluck('name'),
            'data' => $classifications->pluck('total')
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
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
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        
        // Cache key based on period and current date
        $cacheKey = "dashboard_data_{$period}_" . Carbon::now()->format('Y-m-d');
        
        // Cache dashboard data for 1 hour
        $dashboardData = Cache::remember($cacheKey, 3600, function () use ($period) {
            // Get period dates
            $periodDates = $this->getPeriodDates($period);
            
            // Calculate current period totals
            $currentRevenues = Revenue::whereBetween('date', [$periodDates['current_start'], $periodDates['current_end']])->sum('amount');
            $currentExpenses = Expense::whereBetween('date', [$periodDates['current_start'], $periodDates['current_end']])->sum('amount');
            $currentBalance = $currentRevenues - $currentExpenses;

            // Calculate previous period totals
            $previousRevenues = Revenue::whereBetween('date', [$periodDates['previous_start'], $periodDates['previous_end']])->sum('amount');
            $previousExpenses = Expense::whereBetween('date', [$periodDates['previous_start'], $periodDates['previous_end']])->sum('amount');
            $previousBalance = $previousRevenues - $previousExpenses;

            // Calculate growth percentages
            $revenueGrowth = $this->calculateGrowthPercentage($currentRevenues, $previousRevenues);
            $expenseGrowth = $this->calculateGrowthPercentage($currentExpenses, $previousExpenses);
            $balanceGrowth = $this->calculateGrowthPercentage($currentBalance, $previousBalance, true);

            // Get total categories count
            $totalCategories = Category::count();

            // Get chart data
            $balanceChartData = $this->getBalanceChartData($period);
            $expensesCategoryData = $this->getExpensesCategoryChartData($periodDates['current_start'], $periodDates['current_end']);
            
            // Prepare monthly data for charts
            $monthlyData = $this->getMonthlyChartData($balanceChartData);
            $expensesByCategory = $this->getExpensesByCategoryData($expensesCategoryData);

            return [
                'currentRevenues' => $currentRevenues,
                'currentExpenses' => $currentExpenses,
                'currentBalance' => $currentBalance,
                'revenueGrowth' => $revenueGrowth,
                'expenseGrowth' => $expenseGrowth,
                'balanceGrowth' => $balanceGrowth,
                'totalCategories' => $totalCategories,
                'balanceChartData' => $balanceChartData,
                'expensesCategoryData' => $expensesCategoryData,
                'monthlyData' => $monthlyData,
                'expensesByCategory' => $expensesByCategory
            ];
        });

        // Get recent transactions (not cached as they change frequently)
        $recentTransactions = $this->getRecentTransactions();

        return view('dashboard', array_merge($dashboardData, [
            'recentTransactions' => $recentTransactions,
            'period' => $period
        ]))->with([
            'revenueChange' => $dashboardData['revenueGrowth'],
            'expenseChange' => $dashboardData['expenseGrowth'],
            'balance' => $dashboardData['currentBalance'],
            'latestTransactions' => $recentTransactions
        ]);
    }

    /**
     * Get period start and end dates based on period type
     *
     * @param string $period
     * @return array
     */
    private function getPeriodDates(string $period): array
    {
        return match($period) {
            'quarter' => [
                'current_start' => Carbon::now()->startOfQuarter(),
                'current_end' => Carbon::now()->endOfQuarter(),
                'previous_start' => Carbon::now()->subQuarter()->startOfQuarter(),
                'previous_end' => Carbon::now()->subQuarter()->endOfQuarter(),
            ],
            'year' => [
                'current_start' => Carbon::now()->startOfYear(),
                'current_end' => Carbon::now()->endOfYear(),
                'previous_start' => Carbon::now()->subYear()->startOfYear(),
                'previous_end' => Carbon::now()->subYear()->endOfYear(),
            ],
            default => [
                'current_start' => Carbon::now()->startOfMonth(),
                'current_end' => Carbon::now()->endOfMonth(),
                'previous_start' => Carbon::now()->subMonth()->startOfMonth(),
                'previous_end' => Carbon::now()->subMonth()->endOfMonth(),
            ]
        };
    }

    /**
     * Calculate growth percentage between current and previous values
     *
     * @param float $current
     * @param float $previous
     * @param bool $useAbsoluteForBalance
     * @return float
     */
    private function calculateGrowthPercentage(float $current, float $previous, bool $useAbsoluteForBalance = false): float
    {
        if ($previous == 0) {
            return 0;
        }

        $denominator = $useAbsoluteForBalance ? abs($previous) : $previous;
        return round((($current - $previous) / $denominator) * 100, 1);
    }

    /**
     * Get balance chart data for the last 6 periods
     *
     * @param string $period
     * @return array
     */
    private function getBalanceChartData(string $period): array
    {
        $labels = collect();
        $revenues = collect();
        $expenses = collect();

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

            $labels->push(match($period) {
                'quarter' => $date->quarter . 'Q/' . $date->year,
                'year' => (string) $date->year,
                default => $date->format('M/Y')
            });

            $periodRevenues = Revenue::whereBetween('date', [$periodStart, $periodEnd])->sum('amount');
            $periodExpenses = Expense::whereBetween('date', [$periodStart, $periodEnd])->sum('amount');
            
            $revenues->push($periodRevenues);
            $expenses->push($periodExpenses);
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'expenses' => $expenses
        ];
    }

    /**
     * Get expenses by category chart data
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getExpensesCategoryChartData(Carbon $startDate, Carbon $endDate): array
    {
        $expensesByCategory = Expense::select('categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'expenses.fonte_id', '=', 'categories.id')
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $expensesByCategory->pluck('name'),
            'data' => $expensesByCategory->pluck('total')
        ];
    }

    /**
     * Get recent transactions with proper eager loading
     *
     * @return \Illuminate\Support\Collection
     */
    private function getRecentTransactions()
    {
        // Get recent revenues with relationships
        $recentRevenues = Revenue::with(['fonte', 'acao'])
            ->latest('date')
            ->take(5)
            ->get()
            ->map(function($revenue) {
                return (object) [
                    'date' => $revenue->date,
                    'description' => $revenue->description,
                    'amount' => $revenue->amount,
                    'type' => 'Receita',
                    'badge_color' => 'success',
                    'category' => $revenue->acao?->name ?? $revenue->fonte?->name ?? 'N/A'
                ];
            });

        // Get recent expenses with relationships
        $recentExpenses = Expense::with(['fonte', 'acao'])
            ->latest('date')
            ->take(5)
            ->get()
            ->map(function($expense) {
                return (object) [
                    'date' => $expense->date,
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'type' => 'Despesa',
                    'badge_color' => 'danger',
                    'category' => $expense->acao?->name ?? $expense->fonte?->name ?? 'N/A'
                ];
            });

        return $recentRevenues->merge($recentExpenses)
            ->sortByDesc('date')
            ->take(10)
            ->values();
    }

    /**
     * Transform balance chart data to monthly data format for JavaScript charts
     *
     * @param array $balanceChartData
     * @return array
     */
    private function getMonthlyChartData(array $balanceChartData): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < count($balanceChartData['labels']); $i++) {
            $monthlyData[] = [
                'month' => $balanceChartData['labels'][$i],
                'revenues' => $balanceChartData['revenues'][$i],
                'expenses' => $balanceChartData['expenses'][$i]
            ];
        }
        
        return $monthlyData;
    }

    /**
     * Transform expenses category data to format expected by JavaScript charts
     *
     * @param array $expensesCategoryData
     * @return array
     */
    private function getExpensesByCategoryData(array $expensesCategoryData): array
    {
        $expensesByCategory = [];
        
        for ($i = 0; $i < count($expensesCategoryData['labels']); $i++) {
            $expensesByCategory[] = [
                'name' => $expensesCategoryData['labels'][$i],
                'total' => $expensesCategoryData['data'][$i]
            ];
        }
        
        return $expensesByCategory;
    }
}
<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ExpenseClassification;
use App\Models\Revenue;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Prepare revenue report data
     */
    public function prepareRevenueReport(array $filters): array
    {
        $query = Revenue::query()
            ->select(
                DB::raw('DATE(date) as date'),
                'revenues.amount',
                'f.name as fonte',
                'b.name as bloco',
                'g.name as grupo',
                'a.name as acao'
            )
            ->leftJoin('categories as f', 'revenues.fonte_id', '=', 'f.id')
            ->leftJoin('categories as b', 'revenues.bloco_id', '=', 'b.id')
            ->leftJoin('categories as g', 'revenues.grupo_id', '=', 'g.id')
            ->leftJoin('categories as a', 'revenues.acao_id', '=', 'a.id')
            ->whereBetween('date', [$filters['start_date'], $filters['end_date']]);

        $this->applyFilters($query, $filters);

        $items = $this->groupResults($query, $filters['group_by']);

        return [
            'items' => $items,
            'filters' => $filters,
            'metadata' => $this->getMetadata($filters, 'Relatório de Receitas'),
            'charts' => $filters['include_charts'] ?? false ? $this->prepareChartData($items, 'revenues') : null
        ];
    }

    /**
     * Prepare expense report data
     */
    public function prepareExpenseReport(array $filters): array
    {
        $query = Expense::query()
            ->select(
                DB::raw('DATE(date) as date'),
                'expenses.amount',
                'f.name as fonte',
                'b.name as bloco',
                'g.name as grupo',
                'a.name as acao'
            )
            ->leftJoin('categories as f', 'expenses.fonte_id', '=', 'f.id')
            ->leftJoin('categories as b', 'expenses.bloco_id', '=', 'b.id')
            ->leftJoin('categories as g', 'expenses.grupo_id', '=', 'g.id')
            ->leftJoin('categories as a', 'expenses.acao_id', '=', 'a.id')
            ->whereBetween('date', [$filters['start_date'], $filters['end_date']]);

        $this->applyFilters($query, $filters);

        if (!empty($filters['expense_classification_id'])) {
            $query->where('expense_classification_id', $filters['expense_classification_id']);
        }

        $items = $this->groupResults($query, $filters['group_by']);

        return [
            'items' => $items,
            'filters' => $filters,
            'metadata' => $this->getMetadata($filters, 'Relatório de Despesas'),
            'charts' => $filters['include_charts'] ?? false ? $this->prepareChartData($items, 'expenses') : null
        ];
    }

    /**
     * Prepare balance report data
     */
    public function prepareBalanceReport(array $filters): array
    {
        $revenueData = $this->prepareRevenueReport($filters);
        $expenseData = $this->prepareExpenseReport($filters);

        $items = $this->mergeBalanceData($revenueData['items'], $expenseData['items']);

        return [
            'items' => $items,
            'filters' => $filters,
            'metadata' => $this->getMetadata($filters, 'Balanço Financeiro'),
            'charts' => $filters['include_charts'] ?? false ? $this->prepareBalanceChartData($items) : null
        ];
    }

    /**
     * Apply category filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['action_id'])) {
            $query->where('acao_id', $filters['action_id']);
        } elseif (!empty($filters['group_id'])) {
            $query->where('grupo_id', $filters['group_id']);
        } elseif (!empty($filters['block_id'])) {
            $query->where('bloco_id', $filters['block_id']);
        } elseif (!empty($filters['category_id'])) {
            $query->where('fonte_id', $filters['category_id']);
        }
    }

    /**
     * Group query results by period
     */
    private function groupResults($query, string $groupBy): Collection
    {
        return match($groupBy) {
            'daily' => $query->get()->groupBy(function($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            })->map(function($group) {
                return $this->mapGroupedData($group);
            })->values(),
            'monthly' => $query->get()->groupBy(function($item) {
                return Carbon::parse($item->date)->format('Y-m');
            })->map(function($group) {
                return $this->mapGroupedData($group);
            })->values(),
            'yearly' => $query->get()->groupBy(function($item) {
                return Carbon::parse($item->date)->format('Y');
            })->map(function($group) {
                return $this->mapGroupedData($group);
            })->values(),
        };
    }

    /**
     * Map grouped data
     */
    private function mapGroupedData($group): array
    {
        return [
            'period' => Carbon::parse($group->first()->date)->format('Y-m-d'),
            'fonte' => $group->first()->fonte,
            'bloco' => $group->first()->bloco,
            'grupo' => $group->first()->grupo,
            'acao' => $group->first()->acao,
            'total' => $group->sum('amount')
        ];
    }

    /**
     * Merge revenue and expense data for balance report
     */
    private function mergeBalanceData(Collection $revenues, Collection $expenses): Collection
    {
        $periods = $revenues->pluck('period')->merge($expenses->pluck('period'))->unique();
        
        return $periods->map(function($period) use ($revenues, $expenses) {
            $revenue = $revenues->firstWhere('period', $period);
            $expense = $expenses->firstWhere('period', $period);
            
            $revenueAmount = $revenue['total'] ?? 0;
            $expenseAmount = $expense['total'] ?? 0;
            
            return [
                'period' => $period,
                'revenues' => $revenueAmount,
                'expenses' => $expenseAmount,
                'balance' => $revenueAmount - $expenseAmount
            ];
        })->values();
    }

    /**
     * Prepare chart data for reports
     */
    private function prepareChartData(Collection $items, string $type): array
    {
        return [
            'labels' => $items->pluck('period')->toArray(),
            'datasets' => [
                [
                    'label' => ucfirst($type),
                    'data' => $items->pluck('total')->toArray(),
                    'backgroundColor' => $type === 'revenues' ? '#19875444' : '#dc354544',
                    'borderColor' => $type === 'revenues' ? '#198754' : '#dc3545',
                ]
            ]
        ];
    }

    /**
     * Prepare balance chart data
     */
    private function prepareBalanceChartData(Collection $items): array
    {
        return [
            'labels' => $items->pluck('period')->toArray(),
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'data' => $items->pluck('revenues')->toArray(),
                    'borderColor' => '#198754',
                    'backgroundColor' => '#19875422',
                    'type' => 'line'
                ],
                [
                    'label' => 'Despesas',
                    'data' => $items->pluck('expenses')->toArray(),
                    'borderColor' => '#dc3545',
                    'backgroundColor' => '#dc354522',
                    'type' => 'line'
                ],
                [
                    'label' => 'Saldo',
                    'data' => $items->pluck('balance')->toArray(),
                    'backgroundColor' => '#0d6efd44',
                    'borderColor' => '#0d6efd',
                    'type' => 'bar'
                ]
            ]
        ];
    }

    /**
     * Get report metadata
     */
    private function getMetadata(array $filters, string $title): array
    {
        return [
            'generated_at' => now(),
            'title' => $title,
            'type' => $this->getReportTypeName($filters['report_type']),
            'period' => "De " . Carbon::parse($filters['start_date'])->format('d/m/Y') . 
                       " até " . Carbon::parse($filters['end_date'])->format('d/m/Y'),
            'group_by' => $this->getGroupByName($filters['group_by']),
            'category' => !empty($filters['category_id']) ? 
                         Category::find($filters['category_id'])?->name : null,
            'classification' => !empty($filters['expense_classification_id']) ? 
                               ExpenseClassification::find($filters['expense_classification_id'])?->name : null
        ];
    }

    /**
     * Get report type name
     */
    private function getReportTypeName(string $type): string
    {
        return match($type) {
            'revenues' => 'Receitas',
            'expenses' => 'Despesas',
            'balance' => 'Balanço',
            'expense_classification' => 'Classificação de Despesas',
            default => 'Desconhecido'
        };
    }

    /**
     * Get group by name
     */
    private function getGroupByName(string $groupBy): string
    {
        return match($groupBy) {
            'daily' => 'Diário',
            'monthly' => 'Mensal',
            'yearly' => 'Anual',
            default => 'Desconhecido'
        };
    }
}
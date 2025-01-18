<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinancialReport;

class ReportController extends Controller
{
    public function index()
    {
        $categories = Category::where('type', 'fonte')->get();
        return view('reports.index', compact('categories'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:revenues,expenses,balance',
            'category_id' => 'nullable|exists:categories,id',
            'group_by' => 'required|in:daily,monthly,yearly',
            'include_charts' => 'boolean',
            'format' => 'required|in:pdf,excel,html'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Preparar dados base
        $data = $this->prepareReportData($request->report_type, $startDate, $endDate, $request->category_id, $request->group_by);
        
        // Adicionar dados de gráficos se solicitado
        if ($request->include_charts) {
            $data['charts'] = $this->prepareChartData($data['items'], $request->report_type, $request->group_by);
        }

        // Adicionar metadados do relatório
        $data['metadata'] = [
            'generated_at' => now(),
            'period' => "De {$startDate->format('d/m/Y')} até {$endDate->format('d/m/Y')}",
            'type' => $this->getReportTypeName($request->report_type),
            'group_by' => $this->getGroupByName($request->group_by)
        ];

        // Gerar relatório no formato solicitado
        switch ($request->format) {
            case 'pdf':
                return $this->generatePDF($data);
            case 'excel':
                return $this->generateExcel($data);
            default:
                return view('reports.show', $data);
        }
    }

    private function prepareReportData($type, $startDate, $endDate, $categoryId, $groupBy)
    {
        $groupByFormat = match($groupBy) {
            'daily' => '%Y-%m-%d',
            'monthly' => '%Y-%m',
            'yearly' => '%Y'
        };

        if ($type !== 'balance') {
            $query = match($type) {
                'revenues' => Revenue::query(),
                'expenses' => Expense::query(),
            };

            $items = $query->whereBetween('date', [$startDate, $endDate])
                ->when($categoryId, function($q) use ($categoryId) {
                    $q->where('fonte_id', $categoryId);
                })
                ->select(DB::raw("DATE_FORMAT(date, '$groupByFormat') as period"), DB::raw('SUM(amount) as total'))
                ->groupBy(DB::raw("DATE_FORMAT(date, '$groupByFormat')"))
                ->orderBy('period')
                ->get();
        } else {
            // Para relatório de balanço, buscar receitas e despesas
            $revenues = Revenue::whereBetween('date', [$startDate, $endDate])
                ->when($categoryId, function($q) use ($categoryId) {
                    $q->where('fonte_id', $categoryId);
                })
                ->select(DB::raw("DATE_FORMAT(date, '$groupByFormat') as period"), DB::raw('SUM(amount) as total'))
                ->groupBy(DB::raw("DATE_FORMAT(date, '$groupByFormat')"))
                ->orderBy('period');

            $expenses = Expense::whereBetween('date', [$startDate, $endDate])
                ->when($categoryId, function($q) use ($categoryId) {
                    $q->where('fonte_id', $categoryId);
                })
                ->select(DB::raw("DATE_FORMAT(date, '$groupByFormat') as period"), DB::raw('SUM(amount) as total'))
                ->groupBy(DB::raw("DATE_FORMAT(date, '$groupByFormat')"))
                ->orderBy('period');

            // Combinar resultados
            $revenueData = $revenues->get()->keyBy('period');
            $expenseData = $expenses->get()->keyBy('period');
            $allPeriods = collect($revenueData->keys()->merge($expenseData->keys())->unique()->sort()->values());

            $items = $allPeriods->map(function($period) use ($revenueData, $expenseData) {
                return [
                    'period' => $period,
                    'revenues' => $revenueData->get($period)?->total ?? 0,
                    'expenses' => $expenseData->get($period)?->total ?? 0,
                    'balance' => ($revenueData->get($period)?->total ?? 0) - ($expenseData->get($period)?->total ?? 0)
                ];
            });
        }

        return [
            'items' => $items,
            'type' => $type,
            'group_by' => $groupBy
        ];
    }

    private function prepareChartData($items, $type, $groupBy)
    {
        $labels = $items->pluck('period')->toArray();
        
        if ($type === 'balance') {
            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Receitas',
                        'data' => $items->pluck('revenues')->toArray(),
                        'borderColor' => '#198754',
                        'type' => 'line'
                    ],
                    [
                        'label' => 'Despesas',
                        'data' => $items->pluck('expenses')->toArray(),
                        'borderColor' => '#dc3545',
                        'type' => 'line'
                    ],
                    [
                        'label' => 'Saldo',
                        'data' => $items->pluck('balance')->toArray(),
                        'backgroundColor' => '#0d6efd',
                        'type' => 'bar'
                    ]
                ]
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $this->getReportTypeName($type),
                    'data' => $items->pluck('total')->toArray(),
                    'backgroundColor' => $type === 'revenues' ? '#198754' : '#dc3545',
                    'borderColor' => $type === 'revenues' ? '#198754' : '#dc3545',
                ]
            ]
        ];
    }

    private function generatePDF($data)
    {
        $pdf = PDF::loadView('reports.pdf', $data);
        $filename = 'relatorio_' . now()->format('Y-m-d_His') . '.pdf';
        return $pdf->download($filename);
    }

    private function generateExcel($data)
    {
        $filename = 'relatorio_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new FinancialReport($data), $filename);
    }

    private function getReportTypeName($type)
    {
        return match($type) {
            'revenues' => 'Receitas',
            'expenses' => 'Despesas',
            'balance' => 'Balanço',
            default => 'Desconhecido'
        };
    }

    private function getGroupByName($groupBy)
    {
        return match($groupBy) {
            'daily' => 'Diário',
            'monthly' => 'Mensal',
            'yearly' => 'Anual',
            default => 'Desconhecido'
        };
    }
} 
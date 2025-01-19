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
use App\Models\ExpenseClassification;
use App\Models\Transaction;

class ReportController extends Controller
{
    public function index()
    {
        $categories = Category::fontes()->with('children')->get();
        $expenseClassifications = ExpenseClassification::all();
        return view('reports.index', compact('categories', 'expenseClassifications'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'report_type' => 'required|in:revenues,expenses,balance',
            'category_id' => 'nullable|exists:categories,id',
            'block_id' => 'nullable|exists:categories,id',
            'group_id' => 'nullable|exists:categories,id',
            'action_id' => 'nullable|exists:categories,id',
            'expense_classification_id' => 'nullable|exists:expense_classifications,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'required|in:daily,monthly,yearly',
            'include_charts' => 'nullable|boolean',
            'format' => 'required|in:view,pdf,excel'
        ]);

        // Preparar dados do relatório
        $reportData = $this->prepareReportData($data);

        // Adicionar gráficos se solicitado
        if ($request->include_charts) {
            $reportData['charts'] = $this->prepareChartData($reportData['items'], $data['report_type'], $data['group_by']);
        }

        // Gerar relatório no formato solicitado
        switch ($request->format) {
            case 'pdf':
                return $this->generatePDF($reportData);
            case 'excel':
                return $this->generateExcel($reportData);
            default:
                return view('reports.show', $reportData);
        }
    }

    private function prepareReportData($filters)
    {
        \Log::info('Iniciando prepareReportData com filtros:', $filters);

        $groupByFormat = match($filters['group_by']) {
            'daily' => '%Y-%m-%d',
            'monthly' => '%Y-%m',
            'yearly' => '%Y'
        };

        $query = Transaction::query()
            ->whereBetween('date', [$filters['start_date'], $filters['end_date']]);

        if ($filters['report_type'] !== 'balance') {
            $query->where('type', $filters['report_type'] === 'revenues' ? 'revenue' : 'expense');
        }

        // Aplicar filtros de categoria
        if (!empty($filters['action_id'])) {
            $query->where('category_id', $filters['action_id']);
        } elseif (!empty($filters['group_id'])) {
            $categoryIds = Category::where('id', $filters['group_id'])
                ->orWhere('parent_id', $filters['group_id'])
                ->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        } elseif (!empty($filters['block_id'])) {
            $groupIds = Category::where('parent_id', $filters['block_id'])->pluck('id');
            $categoryIds = Category::whereIn('parent_id', $groupIds)
                ->orWhere('id', $filters['block_id'])
                ->orWhereIn('id', $groupIds)
                ->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        } elseif (!empty($filters['category_id'])) {
            $blockIds = Category::where('parent_id', $filters['category_id'])->pluck('id');
            $groupIds = Category::whereIn('parent_id', $blockIds)->pluck('id');
            $categoryIds = Category::whereIn('parent_id', $groupIds)
                ->orWhere('id', $filters['category_id'])
                ->orWhereIn('id', $blockIds)
                ->orWhereIn('id', $groupIds)
                ->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        }

        \Log::info('Query após filtros de categoria:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        if (!empty($filters['expense_classification_id']) && $filters['report_type'] === 'expenses') {
            $query->where('expense_classification_id', $filters['expense_classification_id']);
        }

        // Agrupar dados
        if ($filters['report_type'] !== 'balance') {
            $items = $query->select(
                DB::raw("DATE_FORMAT(date, '$groupByFormat') as period"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw("DATE_FORMAT(date, '$groupByFormat')"))
            ->orderBy('period')
            ->get();

            \Log::info('Resultados da query:', [
                'count' => $items->count(),
                'items' => $items->toArray()
            ]);
        } else {
            // Para balanço, separar receitas e despesas
            $revenues = clone $query;
            $expenses = clone $query;

            $revenueData = $revenues->where('type', 'revenue')
                ->select(
                    DB::raw("DATE_FORMAT(date, '$groupByFormat') as period"),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy(DB::raw("DATE_FORMAT(date, '$groupByFormat')"))
                ->get()
                ->keyBy('period');

            $expenseData = $expenses->where('type', 'expense')
                ->select(
                    DB::raw("DATE_FORMAT(date, '$groupByFormat') as period"),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy(DB::raw("DATE_FORMAT(date, '$groupByFormat')"))
                ->get()
                ->keyBy('period');

            \Log::info('Resultados do balanço:', [
                'receitas_count' => $revenueData->count(),
                'despesas_count' => $expenseData->count(),
                'receitas' => $revenueData->toArray(),
                'despesas' => $expenseData->toArray()
            ]);

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
            'filters' => $filters,
            'metadata' => [
                'generated_at' => now(),
                'period' => "De " . Carbon::parse($filters['start_date'])->format('d/m/Y') . " até " . Carbon::parse($filters['end_date'])->format('d/m/Y'),
                'type' => $this->getReportTypeName($filters['report_type']),
                'group_by' => $this->getGroupByName($filters['group_by'])
            ]
        ];
    }

    private function prepareChartData($items, $reportType, $groupBy)
    {
        $labels = $items->pluck('period')->map(function($period) use ($groupBy) {
            return match($groupBy) {
                'daily' => Carbon::createFromFormat('Y-m-d', $period)->format('d/m/Y'),
                'monthly' => Carbon::createFromFormat('Y-m', $period)->format('M/Y'),
                'yearly' => $period
            };
        })->toArray();
        
        if ($reportType === 'balance') {
            return [
                'labels' => $labels,
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
                        'backgroundColor' => function($context) {
                            return $context['raw'] >= 0 ? '#0d6efd44' : '#dc354544';
                        },
                        'borderColor' => function($context) {
                            return $context['raw'] >= 0 ? '#0d6efd' : '#dc3545';
                        },
                        'type' => 'bar'
                    ]
                ]
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $this->getReportTypeName($reportType),
                    'data' => $items->pluck('total')->toArray(),
                    'backgroundColor' => $reportType === 'revenues' ? '#19875444' : '#dc354544',
                    'borderColor' => $reportType === 'revenues' ? '#198754' : '#dc3545',
                ]
            ]
        ];
    }

    private function generatePDF($data)
    {
        try {
            \Log::info('Iniciando geração do PDF', ['metadata' => $data['metadata']]);
            
            // Configurar o DomPDF
            $config = [
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultMediaType' => 'print',
                'defaultPaperSize' => 'a4',
                'defaultPaperOrientation' => 'portrait'
            ];
            
            // Carregar a view com as configurações
            \Log::info('Carregando view do PDF com configurações', ['config' => $config]);
            $pdf = PDF::setOptions($config)->loadView('reports.pdf', $data);
            \Log::info('View do PDF carregada com sucesso');
            
            // Configurar margens (em milímetros)
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('margin-top', 10);
            $pdf->setOption('margin-right', 10);
            $pdf->setOption('margin-bottom', 10);
            $pdf->setOption('margin-left', 10);
            \Log::info('Configurações do PDF aplicadas');
            
            // Gerar nome do arquivo
            $filename = 'relatorio_' . now()->format('Y-m-d_His') . '.pdf';
            \Log::info('Nome do arquivo gerado', ['filename' => $filename]);
            
            // Retornar o download
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
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
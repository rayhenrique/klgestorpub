<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CitySetting;
use App\Models\ExpenseClassification;
use App\Models\Revenue;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinancialReport;

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
        try {
            \Log::info('Iniciando geração de relatório', ['request' => $request->all()]);

            $data = $request->validate([
                'report_type' => 'required|in:revenues,expenses,balance,expense_classification',
                'category_id' => 'nullable|exists:categories,id',
                'block_id' => 'nullable|exists:categories,id',
                'group_id' => 'nullable|exists:categories,id',
                'action_id' => 'nullable|exists:categories,id',
                'expense_classification_id' => 'nullable|exists:expense_classifications,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'group_by' => 'required|in:daily,monthly,yearly',
                'format' => 'required|in:view,pdf',  
                'include_charts' => 'nullable|boolean'
            ]);

            \Log::info('Dados validados', ['data' => $data]);

            // Preparar dados do relatório
            $reportData = match($data['report_type']) {
                'revenues' => $this->prepareRevenueReport($data),
                'expenses' => $this->prepareExpenseReport($data),
                'balance' => $this->prepareBalanceReport($data),
                'expense_classification' => $this->prepareExpenseClassificationReport($data),
            };

            \Log::info('Dados do relatório preparados', ['items_count' => count($reportData['items'])]);

            // Adicionar gráficos se solicitado
            if ($request->include_charts) {
                $reportData['charts'] = $this->prepareChartData($reportData['items'], $data['report_type'], $data['group_by']);
            }

            \Log::info('Dados do relatório montados', [
                'metadata' => $reportData['metadata'],
                'items_count' => count($reportData['items'])
            ]);

            // Gerar relatório no formato solicitado
            switch ($request->format) {
                case 'pdf':
                    return $this->generatePDF($reportData);
                case 'excel':
                    return response()->json([
                        'message' => 'Exportação para Excel estará disponível na próxima versão.',
                        'status' => 'info'
                    ]);
                default:
                    return view('reports.show', $reportData);
            }
        } catch (\Exception $e) {
            \Log::error('Erro na geração do relatório: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function prepareRevenueReport($filters)
    {
        try {
            \Log::info('Preparando relatório de receitas', ['filters' => $filters]);
            
            $query = Revenue::query()
                ->with(['fonte', 'bloco', 'grupo', 'acao'])
                ->select(
                    DB::raw('DATE(date) as date'),
                    'revenues.amount',
                    'revenues.fonte_id',
                    'revenues.bloco_id',
                    'revenues.grupo_id',
                    'revenues.acao_id'
                )
                ->whereBetween('date', [$filters['start_date'], $filters['end_date']]);

            if (!empty($filters['action_id'])) {
                $query->where('acao_id', $filters['action_id']);
            } elseif (!empty($filters['group_id'])) {
                $query->where('grupo_id', $filters['group_id']);
            } elseif (!empty($filters['block_id'])) {
                $query->where('bloco_id', $filters['block_id']);
            } elseif (!empty($filters['category_id'])) {
                $query->where('fonte_id', $filters['category_id']);
            }

            $items = match($filters['group_by']) {
                'daily' => $query->get()->groupBy(function($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                })->map(function($group) {
                    $first = $group->first();
                    return [
                        'period' => Carbon::parse($first->date)->format('Y-m-d'),
                        'fonte' => $first->fonte?->name,
                        'bloco' => $first->bloco?->name,
                        'grupo' => $first->grupo?->name,
                        'acao' => $first->acao?->name,
                        'total' => $group->sum('amount')
                    ];
                })->values(),
                'monthly' => $query->get()->groupBy(function($item) {
                    return Carbon::parse($item->date)->format('Y-m');
                })->map(function($group) {
                    $first = $group->first();
                    return [
                        'period' => Carbon::parse($first->date)->format('Y-m'),
                        'fonte' => $first->fonte?->name,
                        'bloco' => $first->bloco?->name,
                        'grupo' => $first->grupo?->name,
                        'acao' => $first->acao?->name,
                        'total' => $group->sum('amount')
                    ];
                })->values(),
                'yearly' => $query->get()->groupBy(function($item) {
                    return Carbon::parse($item->date)->format('Y');
                })->map(function($group) {
                    $first = $group->first();
                    return [
                        'period' => Carbon::parse($first->date)->format('Y'),
                        'fonte' => $first->fonte?->name,
                        'bloco' => $first->bloco?->name,
                        'grupo' => $first->grupo?->name,
                        'acao' => $first->acao?->name,
                        'total' => $group->sum('amount')
                    ];
                })->values(),
            };

            \Log::info('Dados do relatório de receitas', [
                'count' => $items->count(),
                'items' => $items->toArray()
            ]);

            // Preparar dados para o gráfico se solicitado
            $charts = null;
            if (!empty($filters['include_charts'])) {
                $charts = [
                    'labels' => $items->pluck('period')->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Receitas',
                            'data' => $items->pluck('total')->toArray(),
                            'borderColor' => '#198754',
                            'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                            'fill' => true
                        ]
                    ]
                ];
            }

            return [
                'items' => $items,
                'filters' => $filters,
                'metadata' => $this->getMetadata($filters, 'Relatório de Receitas'),
                'charts' => $charts
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao preparar relatório de receitas: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function prepareExpenseReport($filters)
    {
        try {
            \Log::info('Preparando relatório de despesas', ['filters' => $filters]);
            
            $query = Expense::query()
                ->with(['fonte', 'bloco', 'grupo', 'acao', 'expenseClassification'])
                ->select(
                    DB::raw('DATE(date) as date'),
                    'expenses.amount',
                    'expenses.fonte_id',
                    'expenses.bloco_id',
                    'expenses.grupo_id',
                    'expenses.acao_id',
                    'expenses.expense_classification_id'
                )
                ->whereBetween('date', [$filters['start_date'], $filters['end_date']]);

            if (!empty($filters['action_id'])) {
                $query->where('acao_id', $filters['action_id']);
            } elseif (!empty($filters['group_id'])) {
                $query->where('grupo_id', $filters['group_id']);
            } elseif (!empty($filters['block_id'])) {
                $query->where('bloco_id', $filters['block_id']);
            } elseif (!empty($filters['category_id'])) {
                $query->where('fonte_id', $filters['category_id']);
            }

            if (!empty($filters['expense_classification_id'])) {
                $query->where('expense_classification_id', $filters['expense_classification_id']);
            }

            $items = match($filters['group_by']) {
                'daily' => $query->get()->groupBy(function($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                })->map(function($group) {
                    $first = $group->first();
                    return [
                        'period' => Carbon::parse($first->date)->format('Y-m-d'),
                        'fonte' => $first->fonte?->name,
                        'bloco' => $first->bloco?->name,
                        'grupo' => $first->grupo?->name,
                        'acao' => $first->acao?->name,
                        'total' => $group->sum('amount')
                    ];
                })->values(),
                'monthly' => $query->get()->groupBy(function($item) {
                    return Carbon::parse($item->date)->format('Y-m');
                })->map(function($group) {
                    $first = $group->first();
                    return [
                        'period' => Carbon::parse($first->date)->format('Y-m'),
                        'fonte' => $first->fonte?->name,
                        'bloco' => $first->bloco?->name,
                        'grupo' => $first->grupo?->name,
                        'acao' => $first->acao?->name,
                        'total' => $group->sum('amount')
                    ];
                })->values(),
                'yearly' => $query->get()->groupBy(function($item) {
                    return Carbon::parse($item->date)->format('Y');
                })->map(function($group) {
                    $first = $group->first();
                    return [
                        'period' => Carbon::parse($first->date)->format('Y'),
                        'fonte' => $first->fonte?->name,
                        'bloco' => $first->bloco?->name,
                        'grupo' => $first->grupo?->name,
                        'acao' => $first->acao?->name,
                        'total' => $group->sum('amount')
                    ];
                })->values(),
            };

            \Log::info('Dados do relatório de despesas', [
                'count' => $items->count(),
                'items' => $items->toArray()
            ]);

            // Preparar dados para o gráfico se solicitado
            $charts = null;
            if (!empty($filters['include_charts'])) {
                $charts = [
                    'labels' => $items->pluck('period')->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Despesas',
                            'data' => $items->pluck('total')->toArray(),
                            'borderColor' => '#dc3545',
                            'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                            'fill' => true
                        ]
                    ]
                ];
            }

            return [
                'items' => $items,
                'filters' => $filters,
                'metadata' => $this->getMetadata($filters, 'Relatório de Despesas'),
                'charts' => $charts
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao preparar relatório de despesas: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function prepareBalanceReport($filters)
    {
        try {
            \Log::info('Preparando relatório de balanço', ['filters' => $filters]);

            // Receitas
            $revenueQuery = Revenue::query()
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

            // Despesas
            $expenseQuery = Expense::query()
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

            if (!empty($filters['action_id'])) {
                $revenueQuery->where('acao_id', $filters['action_id']);
                $expenseQuery->where('acao_id', $filters['action_id']);
            } elseif (!empty($filters['group_id'])) {
                $revenueQuery->where('grupo_id', $filters['group_id']);
                $expenseQuery->where('grupo_id', $filters['group_id']);
            } elseif (!empty($filters['block_id'])) {
                $revenueQuery->where('bloco_id', $filters['block_id']);
                $expenseQuery->where('bloco_id', $filters['block_id']);
            } elseif (!empty($filters['category_id'])) {
                $revenueQuery->where('fonte_id', $filters['category_id']);
                $expenseQuery->where('fonte_id', $filters['category_id']);
            }

            if (!empty($filters['expense_classification_id'])) {
                $expenseQuery->where('expense_classification_id', $filters['expense_classification_id']);
            }

            $revenues = $revenueQuery->get();
            $expenses = $expenseQuery->get();

            $items = match($filters['group_by']) {
                'daily' => collect([...$revenues, ...$expenses])
                    ->groupBy(function($item) {
                        return Carbon::parse($item->date)->format('Y-m-d');
                    })->map(function($group) {
                        $revenues = $group->whereInstanceOf(Revenue::class)->sum('amount');
                        $expenses = $group->whereInstanceOf(Expense::class)->sum('amount');
                        return [
                            'period' => Carbon::parse($group->first()->date)->format('Y-m-d'),
                            'fonte' => $group->first()->fonte,
                            'bloco' => $group->first()->bloco,
                            'grupo' => $group->first()->grupo,
                            'acao' => $group->first()->acao,
                            'revenues' => $revenues,
                            'expenses' => $expenses,
                            'balance' => $revenues - $expenses
                        ];
                    })->values(),
                'monthly' => collect([...$revenues, ...$expenses])
                    ->groupBy(function($item) {
                        return Carbon::parse($item->date)->format('Y-m');
                    })->map(function($group) {
                        $revenues = $group->whereInstanceOf(Revenue::class)->sum('amount');
                        $expenses = $group->whereInstanceOf(Expense::class)->sum('amount');
                        return [
                            'period' => Carbon::parse($group->first()->date)->format('Y-m'),
                            'fonte' => $group->first()->fonte,
                            'bloco' => $group->first()->bloco,
                            'grupo' => $group->first()->grupo,
                            'acao' => $group->first()->acao,
                            'revenues' => $revenues,
                            'expenses' => $expenses,
                            'balance' => $revenues - $expenses
                        ];
                    })->values(),
                'yearly' => collect([...$revenues, ...$expenses])
                    ->groupBy(function($item) {
                        return Carbon::parse($item->date)->format('Y');
                    })->map(function($group) {
                        $revenues = $group->whereInstanceOf(Revenue::class)->sum('amount');
                        $expenses = $group->whereInstanceOf(Expense::class)->sum('amount');
                        return [
                            'period' => Carbon::parse($group->first()->date)->format('Y'),
                            'fonte' => $group->first()->fonte,
                            'bloco' => $group->first()->bloco,
                            'grupo' => $group->first()->grupo,
                            'acao' => $group->first()->acao,
                            'revenues' => $revenues,
                            'expenses' => $expenses,
                            'balance' => $revenues - $expenses
                        ];
                    })->values(),
            };

            \Log::info('Dados do relatório de balanço', [
                'count' => $items->count(),
                'items' => $items->toArray()
            ]);

            // Preparar dados para o gráfico se solicitado
            $charts = null;
            if (!empty($filters['include_charts'])) {
                $charts = [
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
                'items' => $items,
                'filters' => $filters,
                'metadata' => $this->getMetadata($filters, 'Balanço Financeiro'),
                'charts' => $charts
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao preparar relatório de balanço: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function prepareExpenseClassificationReport($filters)
    {
        try {
            \Log::info('Preparando relatório de classificação de despesas', ['filters' => $filters]);

            $query = Expense::query()
                ->whereBetween('date', [$filters['start_date'], $filters['end_date']]);

            // Aplicar filtros
            if (!empty($filters['action_id'])) {
                $query->where('acao_id', $filters['action_id']);
            }
            if (!empty($filters['group_id'])) {
                $query->where('grupo_id', $filters['group_id']);
            }
            if (!empty($filters['block_id'])) {
                $query->where('bloco_id', $filters['block_id']);
            }
            if (!empty($filters['category_id'])) {
                $query->where('fonte_id', $filters['category_id']);
            }
            if (!empty($filters['expense_classification_id'])) {
                $query->where('expense_classification_id', $filters['expense_classification_id']);
            }

            // Adiciona joins para trazer os nomes das categorias
            $query->join('expense_classifications', 'expenses.expense_classification_id', '=', 'expense_classifications.id')
                  ->join('categories as fonte', 'expenses.fonte_id', '=', 'fonte.id')
                  ->join('categories as bloco', 'expenses.bloco_id', '=', 'bloco.id')
                  ->join('categories as grupo', 'expenses.grupo_id', '=', 'grupo.id')
                  ->join('categories as acao', 'expenses.acao_id', '=', 'acao.id');

            $items = match($filters['group_by']) {
                'daily' => $query->select(
                        DB::raw("DATE_FORMAT(expenses.date, '%Y-%m-%d') as period"),
                        'expenses.description',
                        'expenses.amount as total',
                        'expense_classifications.name as classification',
                        'fonte.name as fonte',
                        'bloco.name as bloco',
                        'grupo.name as grupo',
                        'acao.name as acao'
                    )
                    ->orderBy('expenses.date')
                    ->get(),
                'monthly' => $query->select(
                        DB::raw("DATE_FORMAT(expenses.date, '%Y-%m') as period"),
                        'expense_classifications.name as classification',
                        DB::raw('SUM(expenses.amount) as total')
                    )
                    ->groupBy('expense_classifications.id', 'expense_classifications.name', DB::raw("DATE_FORMAT(expenses.date, '%Y-%m')"))
                    ->orderBy('period')
                    ->orderBy('classification')
                    ->get(),
                'yearly' => $query->select(
                        DB::raw("DATE_FORMAT(expenses.date, '%Y') as period"),
                        'expense_classifications.name as classification',
                        DB::raw('SUM(expenses.amount) as total')
                    )
                    ->groupBy('expense_classifications.id', 'expense_classifications.name', DB::raw("DATE_FORMAT(expenses.date, '%Y')"))
                    ->orderBy('period')
                    ->orderBy('classification')
                    ->get(),
            };

            \Log::info('Dados do relatório de classificação de despesas', [
                'count' => $items->count(),
                'items' => $items->toArray()
            ]);

            // Preparar dados para o gráfico se solicitado
            $charts = null;
            if (!empty($filters['include_charts'])) {
                // Agrupar por classificação
                $chartData = $items->groupBy('classification')
                    ->map(function($group) {
                        return [
                            'label' => $group->first()->classification,
                            'data' => $group->pluck('total')->toArray(),
                        ];
                    })
                    ->values();

                // Gerar cores aleatórias para cada classificação
                $colors = [];
                foreach ($chartData as $index => $dataset) {
                    $hue = ($index * 137.508) % 360; // Distribuição uniforme de cores
                    $colors[] = "hsl($hue, 70%, 50%)";
                }

                $charts = [
                    'labels' => $items->pluck('period')->unique()->values()->toArray(),
                    'datasets' => $chartData->map(function($dataset, $index) use ($colors) {
                        return [
                            'label' => $dataset['label'],
                            'data' => $dataset['data'],
                            'borderColor' => $colors[$index],
                            'backgroundColor' => str_replace(')', ', 0.2)', str_replace('hsl', 'hsla', $colors[$index])),
                            'fill' => true
                        ];
                    })->toArray()
                ];
            }

            return [
                'items' => $items,
                'filters' => $filters,
                'metadata' => $this->getMetadata($filters, 'Relatório por Classificação de Despesas'),
                'charts' => $charts
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao preparar relatório de classificação de despesas: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }



    private function applyFilters($query, $filters)
    {
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

        if (!empty($filters['expense_classification_id']) && in_array($filters['report_type'], ['expenses', 'expense_classification'])) {
            $query->where('expense_classification_id', $filters['expense_classification_id']);
        }
    }

    private function groupResults($query, $groupBy)
    {
        return $query->select($this->getGroupByFields($groupBy))
            ->addSelect(DB::raw('SUM(amount) as total'))
            ->groupBy(DB::raw($this->getGroupByField($groupBy)))
            ->orderBy('period')
            ->get();
    }

    private function getGroupByFields($groupBy)
    {
        return [
            DB::raw(match($groupBy) {
                'daily' => "DATE_FORMAT(date, '%Y-%m-%d') as period",
                'monthly' => "DATE_FORMAT(date, '%Y-%m') as period",
                'yearly' => "DATE_FORMAT(date, '%Y') as period"
            })
        ];
    }

    private function getGroupByField($groupBy)
    {
        return match($groupBy) {
            'daily' => "DATE_FORMAT(date, '%Y-%m-%d')",
            'monthly' => "DATE_FORMAT(date, '%Y-%m')",
            'yearly' => "DATE_FORMAT(date, '%Y')"
        };
    }

    private function getMetadata($filters, $title)
    {
        return [
            'generated_at' => now(),
            'title' => $title,
            'type' => $this->getReportTypeName($filters['report_type']),
            'period' => "De " . Carbon::parse($filters['start_date'])->format('d/m/Y') . " até " . Carbon::parse($filters['end_date'])->format('d/m/Y'),
            'group_by' => $this->getGroupByName($filters['group_by']),
            'category_type' => 'Fonte',
            'category' => Category::find($filters['category_id'])?->name ?? null,
            'classification' => ExpenseClassification::find($filters['expense_classification_id'])?->name ?? null
        ];
    }

    private function getReportTypeName($type)
    {
        return match($type) {
            'revenues' => 'Receitas',
            'expenses' => 'Despesas',
            'balance' => 'Balanço',
            'expense_classification' => 'Classificação de Despesas',
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
        try {
            \Log::info('Iniciando geração do Excel', [
                'metadata' => $data['metadata'],
                'filters' => $data['filters'],
                'items_count' => count($data['items'])
            ]);
            
            $filename = 'relatorio_' . now()->format('Y-m-d_His') . '.xlsx';
            \Log::info('Nome do arquivo Excel gerado', ['filename' => $filename]);
            
            return Excel::download(new FinancialReport($data), $filename);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar Excel: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}
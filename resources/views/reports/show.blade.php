<div class="report-content">
    <div class="report-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>{{ $metadata['title'] }}</h3>
                <p class="text-muted mb-0">
                    <strong>Período:</strong> {{ $metadata['period'] }}<br>
                    @if($metadata['group_by'])
                        <strong>Agrupamento:</strong> {{ $metadata['group_by'] }}
                    @endif
                    @if($metadata['category'])
                        <br><strong>{{ $metadata['category_type'] }}:</strong> {{ $metadata['category'] }}
                    @endif
                    @if($metadata['classification'])
                        <br><strong>Classificação:</strong> {{ $metadata['classification'] }}
                    @endif
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-filter-circle-xmark me-1"></i>Limpar Filtros
                </a>
            </div>
        </div>

        @if(!empty($filters['category_id']) || !empty($filters['block_id']) || !empty($filters['group_id']) || !empty($filters['action_id']) || !empty($filters['expense_classification_id']))
            <div class="alert alert-info mt-3 mb-0">
                <h6 class="alert-heading"><i class="fas fa-filter me-2"></i>Filtros Aplicados:</h6>
                <ul class="list-unstyled mb-0">
                    @if(!empty($filters['category_id']))
                        <li><strong>Fonte:</strong> {{ \App\Models\Category::find($filters['category_id'])->name }}</li>
                    @endif
                    @if(!empty($filters['block_id']))
                        <li><strong>Bloco:</strong> {{ \App\Models\Category::find($filters['block_id'])->name }}</li>
                    @endif
                    @if(!empty($filters['group_id']))
                        <li><strong>Grupo:</strong> {{ \App\Models\Category::find($filters['group_id'])->name }}</li>
                    @endif
                    @if(!empty($filters['action_id']))
                        <li><strong>Ação:</strong> {{ \App\Models\Category::find($filters['action_id'])->name }}</li>
                    @endif
                    @if(!empty($filters['expense_classification_id']))
                        <li><strong>Classificação de Despesa:</strong> {{ \App\Models\ExpenseClassification::find($filters['expense_classification_id'])->name }}</li>
                    @endif
                </ul>
            </div>
        @endif
    </div>

    <div class="report-table">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        @if($filters['report_type'] === 'custom')
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Categoria</th>
                            <th>Classificação</th>
                            <th>Descrição</th>
                            <th class="text-end">Valor</th>
                        @elseif($filters['report_type'] === 'category')
                            <th>Categoria</th>
                            <th class="text-end">Receitas</th>
                            <th class="text-end">Despesas</th>
                            <th class="text-end">Saldo</th>
                        @elseif($filters['report_type'] === 'expense_classification')
                            <th>Classificação</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">%</th>
                        @else
                            <th>Período</th>
                            @if($filters['report_type'] === 'balance')
                                <th>Fonte</th>
                                <th>Bloco</th>
                                <th>Grupo</th>
                                <th>Ação</th>
                                <th class="text-end">Receitas</th>
                                <th class="text-end">Despesas</th>
                                <th class="text-end">Saldo</th>
                            @else
                                <th>Fonte</th>
                                <th>Bloco</th>
                                <th>Grupo</th>
                                <th>Ação</th>
                                <th class="text-end">Total</th>
                            @endif
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            @if($filters['report_type'] === 'custom')
                                <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                                <td>{{ $item['type'] }}</td>
                                <td>{{ $item['category'] }}</td>
                                <td>{{ $item['classification'] }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td class="text-end">R$ {{ number_format($item['amount'], 2, ',', '.') }}</td>
                            @elseif($filters['report_type'] === 'category')
                                <td>{{ $item['category'] }}</td>
                                <td class="text-end">R$ {{ number_format($item['revenues'], 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($item['expenses'], 2, ',', '.') }}</td>
                                <td class="text-end {{ $item['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($item['balance'], 2, ',', '.') }}
                                </td>
                            @elseif($filters['report_type'] === 'expense_classification')
                                <td>{{ $item['classification'] }}</td>
                                <td class="text-end">R$ {{ number_format($item['total'], 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format(($item['total'] / $items->sum('total')) * 100, 1) }}%</td>
                            @else
                                <td>
                                    @if($filters['group_by'] === 'daily')
                                        {{ \Carbon\Carbon::parse($item['period'])->format('d/m/Y') }}
                                    @elseif($filters['group_by'] === 'monthly')
                                        {{ \Carbon\Carbon::parse($item['period'])->format('m/Y') }}
                                    @else
                                        {{ $item['period'] }}
                                    @endif
                                </td>
                                @if($filters['report_type'] === 'balance')
                                    <td>{{ $item['fonte'] ?? '-' }}</td>
                                    <td>{{ $item['bloco'] ?? '-' }}</td>
                                    <td>{{ $item['grupo'] ?? '-' }}</td>
                                    <td>{{ $item['acao'] ?? '-' }}</td>
                                    <td class="text-end">R$ {{ number_format($item['revenues'], 2, ',', '.') }}</td>
                                    <td class="text-end">R$ {{ number_format($item['expenses'], 2, ',', '.') }}</td>
                                    <td class="text-end {{ $item['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        R$ {{ number_format($item['balance'], 2, ',', '.') }}
                                    </td>
                                @else
                                    <td>{{ $item['fonte'] ?? '-' }}</td>
                                    <td>{{ $item['bloco'] ?? '-' }}</td>
                                    <td>{{ $item['grupo'] ?? '-' }}</td>
                                    <td>{{ $item['acao'] ?? '-' }}</td>
                                    <td class="text-end">R$ {{ number_format($item['total'], 2, ',', '.') }}</td>
                                @endif
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $filters['report_type'] === 'custom' ? 6 : ($filters['report_type'] === 'balance' || $filters['report_type'] === 'category' ? 4 : ($filters['report_type'] === 'expense_classification' ? 3 : ($filters['report_type'] === 'balance' ? 7 : 6))) }}" class="text-center">
                                Nenhum registro encontrado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($items->count() > 0)
                    <tfoot>
                        <tr class="table-dark fw-bold">
                            @if($filters['report_type'] === 'custom')
                                <td colspan="5" class="text-end">Total Geral:</td>
                                <td class="text-end">R$ {{ number_format($items->sum('amount'), 2, ',', '.') }}</td>
                            @elseif($filters['report_type'] === 'category')
                                <td class="text-end">Totais:</td>
                                <td class="text-end">R$ {{ number_format($items->sum('revenues'), 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($items->sum('expenses'), 2, ',', '.') }}</td>
                                <td class="text-end {{ $items->sum('balance') >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($items->sum('balance'), 2, ',', '.') }}
                                </td>
                            @elseif($filters['report_type'] === 'expense_classification')
                                <td class="text-end">Total:</td>
                                <td class="text-end">R$ {{ number_format($items->sum('total'), 2, ',', '.') }}</td>
                                <td class="text-end">100%</td>
                            @elseif($filters['report_type'] === 'balance')
                                <td class="text-end">Totais:</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end">R$ {{ number_format($items->sum('revenues'), 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($items->sum('expenses'), 2, ',', '.') }}</td>
                                <td class="text-end {{ $items->sum('balance') >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($items->sum('balance'), 2, ',', '.') }}
                                </td>
                            @else
                                <td class="text-end">Total:</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end">R$ {{ number_format($items->sum('total'), 2, ',', '.') }}</td>
                            @endif
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    @if(isset($charts))
        <div class="report-charts mt-4">
            <div class="card">
                <div class="card-body">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
@media print {
    .sidebar, .navbar, .btn-toolbar, .btn-outline-secondary, .btn-outline-primary {
        display: none !important;
    }
    .container-fluid {
        padding: 0 !important;
    }
    .report-content {
        padding: 20px !important;
    }
    .table {
        font-size: 12px !important;
    }
    .table th, .table td {
        padding: 4px 8px !important;
    }
    @page {
        margin: 1cm;
    }
}
</style>

@if(isset($charts))
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('reportChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: '{{ $filters['report_type'] === "balance" ? "bar" : "line" }}',
                data: @json($charts),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: '{{ $metadata['title'] }}'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
@endif
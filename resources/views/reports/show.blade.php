<div class="report-content">
    <div class="report-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3>{{ $metadata['type'] }}</h3>
                <p class="text-muted mb-0">
                    <strong>Período:</strong> {{ $metadata['period'] }}<br>
                    <strong>Agrupamento:</strong> {{ $metadata['group_by'] }}
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
                        <th>Período</th>
                        @if($filters['report_type'] === 'balance')
                            <th class="text-end">Receitas</th>
                            <th class="text-end">Despesas</th>
                            <th class="text-end">Saldo</th>
                        @else
                            <th class="text-end">Total</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                @if($filters['group_by'] === 'daily')
                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $item->period ?? $item['period'])->format('d/m/Y') }}
                                @elseif($filters['group_by'] === 'monthly')
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $item->period ?? $item['period'])->format('M/Y') }}
                                @else
                                    {{ $item->period ?? $item['period'] }}
                                @endif
                            </td>
                            @if($filters['report_type'] === 'balance')
                                <td class="text-end">R$ {{ number_format($item['revenues'], 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($item['expenses'], 2, ',', '.') }}</td>
                                <td class="text-end {{ $item['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($item['balance'], 2, ',', '.') }}
                                </td>
                            @else
                                <td class="text-end">R$ {{ number_format($item->total ?? $item['total'], 2, ',', '.') }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $filters['report_type'] === 'balance' ? 4 : 2 }}" class="text-center">
                                Nenhum registro encontrado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($items->count() > 0)
                <tfoot>
                    <tr class="table-active fw-bold">
                        <td>Total</td>
                        @if($filters['report_type'] === 'balance')
                            <td class="text-end">R$ {{ number_format($items->sum('revenues'), 2, ',', '.') }}</td>
                            <td class="text-end">R$ {{ number_format($items->sum('expenses'), 2, ',', '.') }}</td>
                            <td class="text-end {{ $items->sum('balance') >= 0 ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($items->sum('balance'), 2, ',', '.') }}
                            </td>
                        @else
                            <td class="text-end">R$ {{ number_format($items->sum('total'), 2, ',', '.') }}</td>
                        @endif
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .btn-toolbar, .btn-outline-secondary {
        display: none !important;
    }
    main {
        margin: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style> 
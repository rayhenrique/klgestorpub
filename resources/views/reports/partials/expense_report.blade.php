@php
    $total = 0;
@endphp

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Período</th>
                @if($filters['group_by'] === 'daily')
                    <th>Descrição</th>
                    <th>Classificação</th>
                    <th>Detalhamento</th>
                @endif
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php
                    $total += $item->total;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->period)->format($filters['group_by'] === 'yearly' ? 'Y' : ($filters['group_by'] === 'monthly' ? 'm/Y' : 'd/m/Y')) }}</td>
                    @if($filters['group_by'] === 'daily')
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->classification }}</td>
                        <td>
                            <small class="d-block text-muted mb-1"><strong>Fonte:</strong> {{ $item->fonte }}</small>
                            <small class="d-block text-muted mb-1"><strong>Bloco:</strong> {{ $item->bloco }}</small>
                            <small class="d-block text-muted mb-1"><strong>Grupo:</strong> {{ $item->grupo }}</small>
                            <small class="d-block text-muted"><strong>Ação:</strong> {{ $item->acao }}</small>
                        </td>
                    @endif
                    <td class="text-end">{{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $filters['group_by'] === 'daily' ? 5 : 2 }}" class="text-center">Nenhum registro encontrado</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="table-dark">
                <td><strong>Total Geral</strong></td>
                @if($filters['group_by'] === 'daily')
                    <td colspan="3"></td>
                @endif
                <td class="text-end"><strong>{{ number_format($total, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>

@if(!empty($charts))
<div class="mt-4">
    <canvas id="expenseChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('expenseChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: @json($charts),
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Despesas por Período'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif

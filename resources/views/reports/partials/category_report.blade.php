@php
    $totalRevenues = 0;
    $totalExpenses = 0;
    $totalBalance = 0;

    $dateFormat = match($filters['group_by']) {
        'yearly' => 'Y',
        'monthly' => 'm/Y',
        'daily' => 'd/m/Y'
    };
@endphp

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Período</th>
                <th>Fonte</th>
                <th>Bloco</th>
                <th>Grupo</th>
                <th>Ação</th>
                <th class="text-end">Receitas</th>
                <th class="text-end">Despesas</th>
                <th class="text-end">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php
                    $totalRevenues += $item['revenues'];
                    $totalExpenses += $item['expenses'];
                    $totalBalance += $item['balance'];

                    $formattedPeriod = match($filters['group_by']) {
                        'daily' => \Carbon\Carbon::createFromFormat('Y-m-d', $item['period'])->format($dateFormat),
                        default => $item['period']
                    };
                @endphp
                <tr>
                    <td>{{ $formattedPeriod }}</td>
                    <td>{{ $item['fonte'] }}</td>
                    <td>{{ $item['bloco'] }}</td>
                    <td>{{ $item['grupo'] }}</td>
                    <td>{{ $item['acao'] }}</td>
                    <td class="text-end {{ $item['revenues'] > 0 ? 'text-success' : '' }}">{{ number_format($item['revenues'], 2, ',', '.') }}</td>
                    <td class="text-end {{ $item['expenses'] > 0 ? 'text-danger' : '' }}">{{ number_format($item['expenses'], 2, ',', '.') }}</td>
                    <td class="text-end {{ $item['balance'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($item['balance'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Nenhum registro encontrado</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="table-dark">
                <td colspan="5"><strong>Total Geral</strong></td>
                <td class="text-end {{ $totalRevenues > 0 ? 'text-success' : '' }}"><strong>{{ number_format($totalRevenues, 2, ',', '.') }}</strong></td>
                <td class="text-end {{ $totalExpenses > 0 ? 'text-danger' : '' }}"><strong>{{ number_format($totalExpenses, 2, ',', '.') }}</strong></td>
                <td class="text-end {{ $totalBalance >= 0 ? 'text-success' : 'text-danger' }}"><strong>{{ number_format($totalBalance, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>

@if(!empty($charts))
<div class="mt-4">
    <canvas id="categoryChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: @json($charts),
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Receitas, Despesas e Saldo por Fonte'
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

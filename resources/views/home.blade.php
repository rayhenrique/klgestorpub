@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Cabeçalho com Boas-vindas -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Painel de Controle</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.location.href='{{ route('reports.index') }}'">
                            <i class="fas fa-chart-line me-2"></i>Relatórios
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Atualizar
                    </button>
                </div>
            </div>

            <!-- Cards de Resumo -->
            <div class="row g-3 mb-4">
                <!-- Receitas do Mês -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-uppercase text-muted mb-2">Receitas do Mês</h6>
                                    <h4 class="mb-0 text-success">R$ {{ number_format($monthlyRevenue ?? 0, 2, ',', '.') }}</h4>
                                </div>
                                <div class="col-auto">
                                    <div class="icon-circle bg-success bg-opacity-10">
                                        <i class="fas fa-arrow-up text-success"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-{{ $revenueGrowth > 0 ? 'success' : 'danger' }} me-2">
                                    <i class="fas fa-{{ $revenueGrowth > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ abs($revenueGrowth) }}%
                                </span>
                                <small class="text-muted">vs mês anterior</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Despesas do Mês -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-uppercase text-muted mb-2">Despesas do Mês</h6>
                                    <h4 class="mb-0 text-danger">R$ {{ number_format($monthlyExpense ?? 0, 2, ',', '.') }}</h4>
                                </div>
                                <div class="col-auto">
                                    <div class="icon-circle bg-danger bg-opacity-10">
                                        <i class="fas fa-arrow-down text-danger"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-{{ $expenseGrowth < 0 ? 'success' : 'danger' }} me-2">
                                    <i class="fas fa-{{ $expenseGrowth < 0 ? 'arrow-down' : 'arrow-up' }}"></i>
                                    {{ abs($expenseGrowth) }}%
                                </span>
                                <small class="text-muted">vs mês anterior</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saldo do Mês -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-uppercase text-muted mb-2">Saldo do Mês</h6>
                                    <h4 class="mb-0 {{ ($monthlyRevenue ?? 0) - ($monthlyExpense ?? 0) >= 0 ? 'text-primary' : 'text-danger' }}">
                                        R$ {{ number_format(($monthlyRevenue ?? 0) - ($monthlyExpense ?? 0), 2, ',', '.') }}
                                    </h4>
                                </div>
                                <div class="col-auto">
                                    <div class="icon-circle bg-primary bg-opacity-10">
                                        <i class="fas fa-balance-scale text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-{{ $balanceGrowth > 0 ? 'success' : 'danger' }} me-2">
                                    <i class="fas fa-{{ $balanceGrowth > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ abs($balanceGrowth) }}%
                                </span>
                                <small class="text-muted">vs mês anterior</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Classificações Principais -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-uppercase text-muted mb-2">Classificações</h6>
                                    <h4 class="mb-0 text-info">{{ $totalClassifications ?? 0 }}</h4>
                                </div>
                                <div class="col-auto">
                                    <div class="icon-circle bg-info bg-opacity-10">
                                        <i class="fas fa-tags text-info"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Total de classificações ativas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row g-3 mb-4">
                <!-- Gráfico de Balanço -->
                <div class="col-12 col-xl-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="mb-0">Balanço Financeiro</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="balanceChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Classificações -->
                <div class="col-12 col-xl-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="mb-0">Despesas por Classificação</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="classificationChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimas Transações -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Últimas Transações</h5>
                            <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-link">Ver todas</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Data</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Descrição</th>
                                        <th scope="col">Categoria</th>
                                        <th scope="col" class="text-end">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions ?? [] as $transaction)
                                    <tr>
                                        <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->type === 'revenue' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $transaction->type === 'revenue' ? 'success' : 'danger' }}">
                                                {{ $transaction->type === 'revenue' ? 'Receita' : 'Despesa' }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->description }}</td>
                                        <td>{{ $transaction->category->name }}</td>
                                        <td class="text-end">R$ {{ number_format($transaction->amount, 2, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3">
                                            <div class="text-muted">
                                                <i class="fas fa-info-circle me-2"></i>Nenhuma transação encontrada
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados para o gráfico de balanço
    const balanceCtx = document.getElementById('balanceChart').getContext('2d');
    new Chart(balanceCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($balanceChart['labels'] ?? []) !!},
            datasets: [
                {
                    label: 'Receitas',
                    data: {!! json_encode($balanceChart['revenues'] ?? []) !!},
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true
                },
                {
                    label: 'Despesas',
                    data: {!! json_encode($balanceChart['expenses'] ?? []) !!},
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
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

    // Dados para o gráfico de classificações
    const classificationCtx = document.getElementById('classificationChart').getContext('2d');
    new Chart(classificationCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($classificationChart['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($classificationChart['data'] ?? []) !!},
                backgroundColor: [
                    '#0d6efd',
                    '#6610f2',
                    '#6f42c1',
                    '#d63384',
                    '#dc3545',
                    '#fd7e14',
                    '#ffc107',
                    '#198754',
                    '#20c997',
                    '#0dcaf0'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
});
</script>
@endpush

<style>
.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-circle i {
    font-size: 1.5rem;
}

@media (max-width: 767.98px) {
    .icon-circle {
        width: 40px;
        height: 40px;
    }

    .icon-circle i {
        font-size: 1.25rem;
    }

    h4.mb-0 {
        font-size: 1.25rem;
    }
}
</style>
@endsection

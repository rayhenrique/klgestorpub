@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="{{ route('reports.generate', [
                            'start_date' => match($period) {
                                'quarter' => now()->startOfQuarter()->format('Y-m-d'),
                                'year' => now()->startOfYear()->format('Y-m-d'),
                                default => now()->startOfMonth()->format('Y-m-d'),
                            },
                            'end_date' => match($period) {
                                'quarter' => now()->endOfQuarter()->format('Y-m-d'),
                                'year' => now()->endOfYear()->format('Y-m-d'),
                                default => now()->endOfMonth()->format('Y-m-d'),
                            },
                            'report_type' => 'balance',
                            'group_by' => match($period) {
                                'year' => 'monthly',
                                'quarter' => 'daily',
                                default => 'daily',
                            },
                            'format' => 'excel',
                            'include_charts' => 0
                        ]) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i>Exportar
                        </a>
                    </div>
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar me-1"></i>
                            <span id="periodoSelecionado">Este Mês</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard', ['period' => 'month']) }}">Este Mês</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard', ['period' => 'quarter']) }}">Este Trimestre</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard', ['period' => 'year']) }}">Este Ano</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Cards de Resumo Financeiro -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-circle bg-success bg-opacity-10">
                                        <i class="fas fa-arrow-up text-success fs-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="card-subtitle mb-1 text-muted">Receitas</h6>
                                    <h4 class="card-title mb-0 text-success">R$ {{ number_format($currentRevenues, 2, ',', '.') }}</h4>
                                    <small class="text-{{ $revenueChange >= 0 ? 'success' : 'danger' }}">
                                        <i class="fas fa-caret-{{ $revenueChange >= 0 ? 'up' : 'down' }}"></i>
                                        {{ number_format(abs($revenueChange), 1, ',', '.') }}% 
                                        @switch($period)
                                            @case('quarter')
                                                este trimestre
                                                @break
                                            @case('year')
                                                este ano
                                                @break
                                            @default
                                                este mês
                                        @endswitch
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-circle bg-danger bg-opacity-10">
                                        <i class="fas fa-arrow-down text-danger fs-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="card-subtitle mb-1 text-muted">Despesas</h6>
                                    <h4 class="card-title mb-0 text-danger">R$ {{ number_format($currentExpenses, 2, ',', '.') }}</h4>
                                    <small class="text-{{ $expenseChange >= 0 ? 'danger' : 'success' }}">
                                        <i class="fas fa-caret-{{ $expenseChange >= 0 ? 'up' : 'down' }}"></i>
                                        {{ number_format(abs($expenseChange), 1, ',', '.') }}% 
                                        @switch($period)
                                            @case('quarter')
                                                este trimestre
                                                @break
                                            @case('year')
                                                este ano
                                                @break
                                            @default
                                                este mês
                                        @endswitch
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-circle bg-primary bg-opacity-10">
                                        <i class="fas fa-wallet text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="card-subtitle mb-1 text-muted">Saldo</h6>
                                    <h4 class="card-title mb-0 text-{{ $balance >= 0 ? 'primary' : 'danger' }}">
                                        R$ {{ number_format(abs($balance), 2, ',', '.') }}
                                    </h4>
                                    <small class="text-primary">
                                        Atualizado hoje
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Receitas vs Despesas</h5>
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="revenueExpenseChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Distribuição por Categoria</h5>
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimas Transações -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Últimas Transações</h5>
                        <a href="#" class="btn btn-sm btn-link">Ver todas</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-nowrap">Data</th>
                                    <th class="text-nowrap">Descrição</th>
                                    <th class="text-nowrap d-none d-sm-table-cell">Tipo</th>
                                    <th class="text-end text-nowrap">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestTransactions as $transaction)
                                    <tr>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="fw-medium">{{ $transaction->description }}</div>
                                            <div class="d-sm-none">
                                                <span class="badge bg-{{ $transaction->badge_color }} badge-sm">{{ $transaction->type }}</span>
                                            </div>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <span class="badge bg-{{ $transaction->badge_color }}">{{ $transaction->type }}</span>
                                        </td>
                                        <td class="text-end text-nowrap text-{{ $transaction->badge_color }} fw-bold">
                                            R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Nenhuma transação encontrada
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Atualizar texto do período selecionado
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const period = urlParams.get('period') || 'month';
    const periodoSelecionado = document.getElementById('periodoSelecionado');
    
    switch(period) {
        case 'quarter':
            periodoSelecionado.textContent = 'Este Trimestre';
            break;
        case 'year':
            periodoSelecionado.textContent = 'Este Ano';
            break;
        default:
            periodoSelecionado.textContent = 'Este Mês';
    }
});

// Dados para os gráficos
const monthlyData = @json($monthlyData);
const expensesByCategory = @json($expensesByCategory);

// Gráfico de Receitas vs Despesas
const revenueExpenseChart = new Chart(
    document.getElementById('revenueExpenseChart'),
    {
        type: 'line',
        data: {
            labels: monthlyData.map(data => data.month),
            datasets: [
                {
                    label: 'Receitas',
                    data: monthlyData.map(data => data.revenues),
                    borderColor: '#198754',
                    tension: 0.1
                },
                {
                    label: 'Despesas',
                    data: monthlyData.map(data => data.expenses),
                    borderColor: '#dc3545',
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    }
);

// Gráfico de Distribuição por Categoria
const categoryChart = new Chart(
    document.getElementById('categoryChart'),
    {
        type: 'doughnut',
        data: {
            labels: expensesByCategory.map(category => category.name),
            datasets: [{
                data: expensesByCategory.map(category => category.total),
                backgroundColor: [
                    '#004a7c',
                    '#005691',
                    '#0073a8',
                    '#0087b3',
                    '#009ac0'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    }
);
</script>
@endpush
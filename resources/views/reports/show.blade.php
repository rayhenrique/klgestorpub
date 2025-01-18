@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div>
                    <h1 class="h2">{{ $metadata['type'] }}</h1>
                    <p class="text-muted">{{ $metadata['period'] }}</p>
                </div>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="{{ request()->fullUrlWithQuery(['format' => 'pdf']) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i>PDF
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['format' => 'excel']) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                            <i class="fas fa-file-excel me-1"></i>Excel
                        </a>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Imprimir
                    </button>
                </div>
            </div>

            @if(isset($charts))
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <canvas id="reportChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Período</th>
                                    @if($type === 'balance')
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
                                            @if($group_by === 'daily')
                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d', $item['period'])->format('d/m/Y') }}
                                            @elseif($group_by === 'monthly')
                                                {{ \Carbon\Carbon::createFromFormat('Y-m', $item['period'])->format('m/Y') }}
                                            @else
                                                {{ $item['period'] }}
                                            @endif
                                        </td>
                                        @if($type === 'balance')
                                            <td class="text-end text-success">R$ {{ number_format($item['revenues'], 2, ',', '.') }}</td>
                                            <td class="text-end text-danger">R$ {{ number_format($item['expenses'], 2, ',', '.') }}</td>
                                            <td class="text-end {{ $item['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                                                R$ {{ number_format(abs($item['balance']), 2, ',', '.') }}
                                            </td>
                                        @else
                                            <td class="text-end {{ $type === 'revenues' ? 'text-success' : 'text-danger' }}">
                                                R$ {{ number_format($item['total'], 2, ',', '.') }}
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $type === 'balance' ? 4 : 2 }}" class="text-center">
                                            Nenhum registro encontrado
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($items->count() > 0)
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td>Total</td>
                                        @if($type === 'balance')
                                            <td class="text-end text-success">
                                                R$ {{ number_format($items->sum('revenues'), 2, ',', '.') }}
                                            </td>
                                            <td class="text-end text-danger">
                                                R$ {{ number_format($items->sum('expenses'), 2, ',', '.') }}
                                            </td>
                                            @php
                                                $totalBalance = $items->sum('revenues') - $items->sum('expenses');
                                            @endphp
                                            <td class="text-end {{ $totalBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                                                R$ {{ number_format(abs($totalBalance), 2, ',', '.') }}
                                            </td>
                                        @else
                                            <td class="text-end {{ $type === 'revenues' ? 'text-success' : 'text-danger' }}">
                                                R$ {{ number_format($items->sum('total'), 2, ',', '.') }}
                                            </td>
                                        @endif
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@if(isset($charts))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('reportChart').getContext('2d');
new Chart(ctx, {
    type: '{{ $type === "balance" ? "bar" : "line" }}',
    data: {
        labels: @json($charts['labels']),
        datasets: @json($charts['datasets'])
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});
</script>
@endpush
@endif

@push('styles')
<style>
@media print {
    .sidebar, .navbar, .btn-toolbar {
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
@endpush
@endsection 
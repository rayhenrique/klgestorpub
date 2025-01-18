@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Relatórios</h1>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('reports.generate') }}" method="GET" target="_blank">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="report_type" class="form-label">Tipo de Relatório</label>
                                    <select class="form-select" id="report_type" name="report_type" required>
                                        <option value="revenues">Receitas</option>
                                        <option value="expenses">Despesas</option>
                                        <option value="balance">Balanço</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Categoria (Fonte)</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">Todas as categorias</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Data Inicial</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Data Final</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="group_by" class="form-label">Agrupar por</label>
                                    <select class="form-select" id="group_by" name="group_by" required>
                                        <option value="daily">Diário</option>
                                        <option value="monthly">Mensal</option>
                                        <option value="yearly">Anual</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="format" class="form-label">Formato</label>
                                    <select class="form-select" id="format" name="format" required>
                                        <option value="html">Visualizar na Tela</option>
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label d-block">Opções</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_charts" name="include_charts" value="1" checked>
                                        <label class="form-check-label" for="include_charts">
                                            Incluir Gráficos
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-alt me-2"></i>Gerar Relatório
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Definir datas padrão (mês atual)
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

    document.getElementById('start_date').value = firstDay.toISOString().split('T')[0];
    document.getElementById('end_date').value = lastDay.toISOString().split('T')[0];

    // Atualizar visibilidade do checkbox de gráficos
    const formatSelect = document.getElementById('format');
    const chartsDiv = document.getElementById('include_charts').closest('.mb-3');

    formatSelect.addEventListener('change', function() {
        chartsDiv.style.display = this.value === 'html' ? 'block' : 'none';
        if (this.value !== 'html') {
            document.getElementById('include_charts').checked = false;
        }
    });
});
</script>
@endpush
@endsection 
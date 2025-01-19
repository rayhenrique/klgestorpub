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
                    <form action="{{ route('reports.generate') }}" method="GET" id="reportForm">
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
                                    <label for="category_id" class="form-label">Fonte</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">Todas as fontes</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="block_id" class="form-label">Bloco</label>
                                    <select class="form-select" id="block_id" name="block_id" disabled>
                                        <option value="">Todos os blocos</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="group_id" class="form-label">Grupo</label>
                                    <select class="form-select" id="group_id" name="group_id" disabled>
                                        <option value="">Todos os grupos</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="action_id" class="form-label">Ação</label>
                                    <select class="form-select" id="action_id" name="action_id" disabled>
                                        <option value="">Todas as ações</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expense_classification_id" class="form-label">Classificação de Despesa</label>
                                    <select class="form-select" id="expense_classification_id" name="expense_classification_id">
                                        <option value="">Todas as classificações</option>
                                        @foreach($expenseClassifications as $classification)
                                            <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Data Inicial</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Data Final</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="group_by" class="form-label">Agrupar por</label>
                                    <select class="form-select" id="group_by" name="group_by" required>
                                        <option value="daily">Diário</option>
                                        <option value="monthly">Mensal</option>
                                        <option value="yearly">Anual</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                        <i class="fas fa-filter-circle-xmark me-2"></i>Limpar Filtros
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="generateReport('view')">
                                        <i class="fas fa-eye me-2"></i>Visualizar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="generateReport('pdf')">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="generateReport('excel')">
                                        <i class="fas fa-file-excel me-2"></i>Exportar Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Container para visualização do relatório -->
            <div id="reportContainer" class="mt-4" style="display: none;">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div id="reportContent"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const blockSelect = document.getElementById('block_id');
    const groupSelect = document.getElementById('group_id');
    const actionSelect = document.getElementById('action_id');
    const reportTypeSelect = document.getElementById('report_type');
    const expenseClassificationSelect = document.getElementById('expense_classification_id');

    // Configuração do CSRF token para requisições AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Definir datas padrão (mês atual)
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    document.getElementById('start_date').value = firstDay.toISOString().split('T')[0];
    document.getElementById('end_date').value = lastDay.toISOString().split('T')[0];

    // Função para fazer requisições AJAX
    async function fetchData(url) {
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Erro na requisição:', error);
            throw error;
        }
    }

    // Função para carregar os blocos quando uma fonte é selecionada
    categorySelect.addEventListener('change', async function() {
        const sourceId = this.value;
        blockSelect.disabled = !sourceId;
        blockSelect.innerHTML = '<option value="">Todos os blocos</option>';
        groupSelect.disabled = true;
        groupSelect.innerHTML = '<option value="">Todos os grupos</option>';
        actionSelect.disabled = true;
        actionSelect.innerHTML = '<option value="">Todas as ações</option>';

        if (sourceId) {
            try {
                const blocks = await fetchData(`/categories/${sourceId}/children`);
                blocks.forEach(block => {
                    if (block.type === 'bloco') {
                        const option = new Option(block.name, block.id);
                        blockSelect.add(option);
                    }
                });
                blockSelect.disabled = false;
            } catch (error) {
                console.error('Erro ao carregar blocos:', error);
                showError('Erro ao carregar os blocos. Por favor, tente novamente.');
            }
        }
    });

    // Função para carregar os grupos quando um bloco é selecionado
    blockSelect.addEventListener('change', async function() {
        const blockId = this.value;
        groupSelect.disabled = !blockId;
        groupSelect.innerHTML = '<option value="">Todos os grupos</option>';
        actionSelect.disabled = true;
        actionSelect.innerHTML = '<option value="">Todas as ações</option>';

        if (blockId) {
            try {
                const groups = await fetchData(`/categories/${blockId}/children`);
                groups.forEach(group => {
                    if (group.type === 'grupo') {
                        const option = new Option(group.name, group.id);
                        groupSelect.add(option);
                    }
                });
                groupSelect.disabled = false;
            } catch (error) {
                console.error('Erro ao carregar grupos:', error);
                showError('Erro ao carregar os grupos. Por favor, tente novamente.');
            }
        }
    });

    // Função para carregar as ações quando um grupo é selecionado
    groupSelect.addEventListener('change', async function() {
        const groupId = this.value;
        actionSelect.disabled = !groupId;
        actionSelect.innerHTML = '<option value="">Todas as ações</option>';

        if (groupId) {
            try {
                const actions = await fetchData(`/categories/${groupId}/children`);
                actions.forEach(action => {
                    if (action.type === 'acao') {
                        const option = new Option(action.name, action.id);
                        actionSelect.add(option);
                    }
                });
                actionSelect.disabled = false;
            } catch (error) {
                console.error('Erro ao carregar ações:', error);
                showError('Erro ao carregar as ações. Por favor, tente novamente.');
            }
        }
    });

    // Mostrar/ocultar classificação de despesa baseado no tipo de relatório
    reportTypeSelect.addEventListener('change', function() {
        const isExpenseReport = this.value === 'expenses';
        expenseClassificationSelect.closest('.col-md-6').style.display = isExpenseReport ? 'block' : 'none';
        if (!isExpenseReport) {
            expenseClassificationSelect.value = '';
        }
    });

    // Trigger inicial para configurar a visibilidade da classificação de despesa
    reportTypeSelect.dispatchEvent(new Event('change'));

    // Função para limpar todos os filtros
    window.clearFilters = function() {
        // Limpar tipo de relatório
        document.getElementById('report_type').value = 'revenues';
        
        // Limpar categorias
        document.getElementById('category_id').value = '';
        document.getElementById('block_id').value = '';
        document.getElementById('block_id').disabled = true;
        document.getElementById('group_id').value = '';
        document.getElementById('group_id').disabled = true;
        document.getElementById('action_id').value = '';
        document.getElementById('action_id').disabled = true;
        
        // Limpar classificação de despesa
        document.getElementById('expense_classification_id').value = '';
        
        // Resetar datas para o mês atual
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        
        document.getElementById('start_date').value = firstDay.toISOString().split('T')[0];
        document.getElementById('end_date').value = lastDay.toISOString().split('T')[0];
        
        // Resetar agrupamento
        document.getElementById('group_by').value = 'daily';

        // Limpar container do relatório se estiver visível
        const reportContainer = document.getElementById('reportContainer');
        if (reportContainer) {
            reportContainer.style.display = 'none';
            const reportContent = document.getElementById('reportContent');
            if (reportContent) {
                reportContent.innerHTML = '';
            }
        }

        // Mostrar notificação
        showSuccess('Filtros limpos com sucesso!');
    }
});

function generateReport(format) {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    formData.append('format', format);

    if (format === 'view') {
        // Para visualização, fazer uma requisição AJAX
        const params = new URLSearchParams(formData);
        const reportContent = document.getElementById('reportContent');
        const reportContainer = document.getElementById('reportContainer');
        
        // Mostrar indicador de carregamento
        reportContent.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Gerando relatório...</p>
            </div>
        `;
        reportContainer.style.display = 'block';

        fetch(form.action + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'text/html,application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(async response => {
            console.log('Status da resposta:', response.status);
            console.log('Headers da resposta:', Object.fromEntries(response.headers.entries()));
            
            const responseText = await response.text();
            console.log('Conteúdo da resposta:', responseText);
            
            if (!response.ok) {
                throw new Error(responseText || `Erro HTTP: ${response.status}`);
            }
            
            return responseText;
        })
        .then(html => {
            console.log('Tamanho do HTML:', html.length);
            if (!html.trim()) {
                throw new Error('O relatório retornou vazio');
            }

            reportContent.innerHTML = html;
            reportContainer.style.display = 'block';
            reportContainer.scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Erro detalhado:', error);
            console.error('Stack trace:', error.stack);
            reportContainer.style.display = 'none';
            showError('Erro ao gerar o relatório. Por favor, tente novamente. Detalhes: ' + error.message);
        });
    } else {
        try {
            // Para PDF e Excel, submeter o formulário normalmente
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'format';
            input.value = format;
            form.appendChild(input);
            
            // Adicionar target _blank para abrir em nova aba
            const originalTarget = form.target;
            form.target = '_blank';
            
            form.submit();
            
            // Restaurar o target original e remover o input
            form.target = originalTarget;
            form.removeChild(input);
        } catch (error) {
            console.error('Erro ao exportar:', error);
            showError('Erro ao exportar o relatório. Por favor, tente novamente.');
        }
    }
}

function showError(message) {
    Swal.fire({
        title: 'Erro',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn btn-primary'
        }
    });
}
</script>
@endpush
@endsection 
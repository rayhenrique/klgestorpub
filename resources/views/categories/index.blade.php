@extends('layouts.app')

@section('content')
<style>
/* Estilos base do accordion */
.accordion-button {
    background-color: #f8f9fa !important;
    color: #212529 !important;
    padding: 1rem !important;
}

/* Estilos quando expandido */
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa !important;
    color: #212529 !important;
    box-shadow: none !important;
}

/* Estilos para ícones e badges */
.accordion-button i,
.accordion-button .badge,
.accordion-button:not(.collapsed) i,
.accordion-button:not(.collapsed) .badge {
    color: inherit !important;
}

/* Remove a rotação da seta */
.accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
    margin-left: 0 !important;
}

.accordion-button:not(.collapsed)::after {
    transform: rotate(-180deg);
}

/* Estilos para o cabeçalho da categoria */
.category-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Estilos para os cards internos */
.card-header {
    background-color: #fff !important;
    color: #212529 !important;
    padding: 0.75rem 1rem !important;
    border-bottom: 1px solid rgba(0,0,0,.125) !important;
}

/* Estilos para os itens dentro do accordion */
.accordion-body .card {
    background-color: #ffffff !important;
    border: 1px solid rgba(0,0,0,.125) !important;
    margin-bottom: 0.5rem !important;
}

/* Ajuste para os badges */
.badge {
    font-weight: 500 !important;
    padding: 0.4em 0.6em !important;
}

.badge.bg-secondary { background-color: #6c757d !important; }
.badge.bg-info { background-color: #0dcaf0 !important; }
.badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge.bg-success { background-color: #198754 !important; }

/* Estilos para os botões de ação */
.btn-sm {
    padding: 0.25rem 0.5rem !important;
    font-size: 0.875rem !important;
    border-radius: 0.2rem !important;
}

.btn-outline-primary:hover, .btn-outline-danger:hover {
    color: #fff !important;
}

/* Ajustes de espaçamento */
.accordion-body {
    padding: 1rem !important;
}

.card-body {
    padding: 1rem !important;
}
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Categorias</h1>
                @if(!auth()->user()->isOperator())
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nova Categoria
                    </a>
                </div>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="accordion" id="categoriesAccordion">
                        @foreach($fontes as $fonte)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="fonte{{ $fonte->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSource{{ $fonte->id }}" aria-expanded="false" aria-controls="collapseSource{{ $fonte->id }}">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <div class="category-header">
                                                <i class="fas fa-folder me-2"></i>
                                                <span>{{ $fonte->name }}</span>
                                                <span class="badge bg-secondary ms-2">Fonte</span>
                                            </div>
                                            @if(!auth()->user()->isOperator())
                                            <div class="ms-auto me-3">
                                                <a href="{{ route('categories.edit', $fonte->id) }}" 
                                                   class="btn btn-sm btn-outline-primary me-2" 
                                                   onclick="confirmEdit(event, 'Deseja editar esta fonte?')">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('categories.destroy', $fonte->id) }}" method="POST" class="d-inline" onsubmit="event.stopPropagation();">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, 'Tem certeza que deseja excluir esta categoria?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapseSource{{ $fonte->id }}" class="accordion-collapse collapse" data-bs-parent="#categoriesAccordion">
                                    <div class="accordion-body">
                                        <!-- Blocos -->
                                        <div class="ms-4">
                                            @foreach($fonte->children as $bloco)
                                                <div class="card mb-2">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="fas fa-cube me-2"></i>
                                                                {{ $bloco->name }}
                                                                <span class="badge bg-info ms-2">Bloco</span>
                                                            </div>
                                                            @if(!auth()->user()->isOperator())
                                                            <div>
                                                                <a href="{{ route('categories.edit', $bloco) }}" 
                                                                   class="btn btn-sm btn-outline-primary me-2"
                                                                   onclick="confirmEdit(event, 'Deseja editar este bloco?')">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('categories.destroy', $bloco) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, 'Tem certeza que deseja excluir esta categoria?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <!-- Grupos -->
                                                        <div class="ms-3">
                                                            @foreach($bloco->children as $grupo)
                                                                <div class="card mb-2">
                                                                    <div class="card-header">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <div>
                                                                                <i class="fas fa-layer-group me-2"></i>
                                                                                {{ $grupo->name }}
                                                                                <span class="badge bg-warning ms-2">Grupo</span>
                                                                            </div>
                                                                            @if(!auth()->user()->isOperator())
                                                                            <div>
                                                                                <a href="{{ route('categories.edit', $grupo) }}" 
                                                                                   class="btn btn-sm btn-outline-primary me-2"
                                                                                   onclick="confirmEdit(event, 'Deseja editar este grupo?')">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                                <form action="{{ route('categories.destroy', $grupo) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, 'Tem certeza que deseja excluir esta categoria?')">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-body p-3">
                                                                        <!-- Ações -->
                                                                        <div class="ms-3">
                                                                            @foreach($grupo->children as $acao)
                                                                                <div class="card mb-2">
                                                                                    <div class="card-header">
                                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                                            <div>
                                                                                                <i class="fas fa-play me-2"></i>
                                                                                                {{ $acao->name }}
                                                                                                <span class="badge bg-success ms-2">Ação</span>
                                                                                            </div>
                                                                                            @if(!auth()->user()->isOperator())
                                                                                            <div>
                                                                                                <a href="{{ route('categories.edit', $acao) }}" 
                                                                                                   class="btn btn-sm btn-outline-primary me-2"
                                                                                                   onclick="confirmEdit(event, 'Deseja editar esta ação?')">
                                                                                                    <i class="fas fa-edit"></i>
                                                                                                </a>
                                                                                                <form action="{{ route('categories.destroy', $acao) }}" method="POST" class="d-inline">
                                                                                                    @csrf
                                                                                                    @method('DELETE')
                                                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, 'Tem certeza que deseja excluir esta categoria?')">
                                                                                                        <i class="fas fa-trash"></i>
                                                                                                    </button>
                                                                                                </form>
                                                                                            </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection 
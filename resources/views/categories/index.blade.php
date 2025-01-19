@extends('layouts.app')

@section('content')
<style>
/* Estilos base do accordion */
.accordion-button {
    background-color: #f8f9fa !important;
    color: #212529 !important;
    padding: 1rem !important;
    position: relative;
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
    flex: 1;
}

/* Ajustes nos botões de ação */
.action-buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: auto;
    position: absolute;
    right: 3rem;
}

.action-buttons form {
    margin: 0;
}

/* Ajustes de espaçamento */
.accordion-body {
    padding: 1rem !important;
}

.card-body {
    padding: 1rem !important;
}

/* Ajuste para o container de fonte */
.fonte-container {
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}

/* Ajuste para o cabeçalho de fonte */
.fonte-header {
    background-color: #f8f9fa;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
}

/* Ajuste para o conteúdo de fonte */
.fonte-content {
    padding: 1rem;
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
                    @foreach($fontes as $fonte)
                        <div class="fonte-container">
                            <div class="fonte-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="category-header">
                                        <i class="fas fa-folder me-2"></i>
                                        <span>{{ $fonte->name }}</span>
                                        <span class="badge bg-secondary ms-2">Fonte</span>
                                    </div>
                                    @if(!auth()->user()->isOperator())
                                    <div class="action-buttons">
                                        <a href="{{ route('categories.edit', $fonte->id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           onclick="confirmEdit(event, 'Deseja editar esta fonte?')">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('categories.destroy', $fonte->id) }}" method="POST" class="d-inline">
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
                            <div class="fonte-content">
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
                                                    <div class="action-buttons">
                                                        <a href="{{ route('categories.edit', $bloco) }}" 
                                                           class="btn btn-sm btn-outline-primary"
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
                                                                    <div class="action-buttons">
                                                                        <a href="{{ route('categories.edit', $grupo) }}" 
                                                                           class="btn btn-sm btn-outline-primary"
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
                                                                                    <div class="action-buttons">
                                                                                        <a href="{{ route('categories.edit', $acao) }}" 
                                                                                           class="btn btn-sm btn-outline-primary"
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
                    @endforeach
                </div>
            </div>
        </main>
    </div>
</div>
@endsection 
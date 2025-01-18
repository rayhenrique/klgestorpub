@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Classificação de Despesas</h1>
                @if(!auth()->user()->isOperator())
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('expense-classifications.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nova Classificação
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    @if(!auth()->user()->isOperator())
                                    <th width="120">Ações</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classifications as $classification)
                                    <tr>
                                        <td>{{ $classification->name }}</td>
                                        <td>{{ $classification->code }}</td>
                                        <td>{{ $classification->description }}</td>
                                        @if(!auth()->user()->isOperator())
                                        <td>
                                            <a href="{{ route('expense-classifications.edit', $classification->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('expense-classifications.destroy', $classification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir esta classificação?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhuma classificação encontrada</td>
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
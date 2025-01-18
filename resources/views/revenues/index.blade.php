@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Receitas</h1>
                <a href="{{ route('revenues.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nova Receita
                </a>
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
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Fonte</th>
                                    <th>Bloco</th>
                                    <th>Grupo</th>
                                    <th>Ação</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenues as $revenue)
                                    <tr>
                                        <td>{{ $revenue->date->format('d/m/Y') }}</td>
                                        <td>{{ $revenue->description }}</td>
                                        <td>R$ {{ number_format($revenue->amount, 2, ',', '.') }}</td>
                                        <td>{{ $revenue->fonte->name }}</td>
                                        <td>{{ $revenue->bloco->name }}</td>
                                        <td>{{ $revenue->grupo->name }}</td>
                                        <td>{{ $revenue->acao->name }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('revenues.edit', $revenue) }}" 
                                               class="btn btn-sm btn-outline-primary me-2"
                                               onclick="confirmEdit(event, 'Deseja editar esta receita?')">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('revenues.destroy', $revenue) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete(event, 'Tem certeza que deseja excluir esta receita?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Nenhuma receita cadastrada.</td>
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
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Despesas</h1>
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nova Despesa
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Classificação</th>
                                    <th>Fonte</th>
                                    <th>Bloco</th>
                                    <th>Grupo</th>
                                    <th>Ação</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->date->format('d/m/Y') }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td>R$ {{ number_format($expense->amount, 2, ',', '.') }}</td>
                                        <td>{{ $expense->classification->name }}</td>
                                        <td>{{ $expense->fonte->name }}</td>
                                        <td>{{ $expense->bloco->name }}</td>
                                        <td>{{ $expense->grupo->name }}</td>
                                        <td>{{ $expense->acao->name }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('expenses.edit', $expense) }}" 
                                               class="btn btn-sm btn-outline-primary me-2"
                                               onclick="confirmEdit(event, 'Deseja editar esta despesa?')">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('expenses.destroy', $expense) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete(event, 'Tem certeza que deseja excluir esta despesa?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Nenhuma despesa cadastrada.</td>
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
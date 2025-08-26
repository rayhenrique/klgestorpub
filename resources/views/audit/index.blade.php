@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Logs de Auditoria</h1>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('audit.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="model_type" class="form-label">Tipo</label>
                            <select name="model_type" id="model_type" class="form-select">
                                <option value="">Todos</option>
                                @foreach($modelTypes as $type)
                                    <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>
                                        {{ str_replace('App\\Models\\', '', $type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="action" class="form-label">Ação</label>
                            <select name="action" id="action" class="form-select">
                                <option value="">Todas</option>
                                <option value="edit" {{ request('action') == 'edit' ? 'selected' : '' }}>Edição</option>
                                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Exclusão</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="user_id" class="form-label">Usuário</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="date_start" class="form-label">Data Inicial</label>
                            <input type="date" class="form-control" id="date_start" name="date_start" 
                                value="{{ request('date_start') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="date_end" class="form-label">Data Final</label>
                            <input type="date" class="form-control" id="date_end" name="date_end" 
                                value="{{ request('date_end') }}">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ route('audit.index') }}" class="btn btn-secondary">Limpar</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela de Logs -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Tipo</th>
                            <th>Ação</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $log->user->name }}</td>
                                <td>{{ str_replace('App\\Models\\', '', $log->model_type) }}</td>
                                <td>
                                    @if($log->action == 'edit')
                                        <span class="badge bg-warning">Edição</span>
                                    @else
                                        <span class="badge bg-danger">Exclusão</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('audit.show', $log) }}" class="btn btn-sm btn-info">
                                        Ver Detalhes
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhum log encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center">
                {{ $logs->links() }}
            </div>
        </main>
    </div>
</div>
@endsection 
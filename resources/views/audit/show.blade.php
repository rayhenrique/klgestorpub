@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Detalhes do Log</h1>
                <a href="{{ route('audit.index') }}" class="btn btn-secondary">Voltar</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Informações Gerais</h5>
                            <dl class="row">
                                <dt class="col-sm-4">Data/Hora:</dt>
                                <dd class="col-sm-8">{{ $log->created_at->format('d/m/Y H:i:s') }}</dd>

                                <dt class="col-sm-4">Usuário:</dt>
                                <dd class="col-sm-8">{{ $log->user->name }}</dd>

                                <dt class="col-sm-4">Tipo:</dt>
                                <dd class="col-sm-8">{{ str_replace('App\\Models\\', '', $log->model_type) }}</dd>

                                <dt class="col-sm-4">Ação:</dt>
                                <dd class="col-sm-8">
                                    @if($log->action == 'edit')
                                        <span class="badge bg-warning">Edição</span>
                                    @else
                                        <span class="badge bg-danger">Exclusão</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>

                    @if($log->action == 'edit')
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Valores Anteriores</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Campo</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($log->old_values as $field => $value)
                                                <tr>
                                                    <td>{{ $field }}</td>
                                                    <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Novos Valores</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Campo</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($log->new_values as $field => $value)
                                                <tr>
                                                    <td>{{ $field }}</td>
                                                    <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-12">
                                <h5>Dados Excluídos</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Campo</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($log->old_values as $field => $value)
                                                <tr>
                                                    <td>{{ $field }}</td>
                                                    <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection 
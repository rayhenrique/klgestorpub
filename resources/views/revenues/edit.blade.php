@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Editar Receita</h1>
                <a href="{{ route('revenues.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('revenues.update', $revenue) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('description') is-invalid @enderror" 
                                       id="description" 
                                       name="description" 
                                       value="{{ old('description', $revenue->description) }}" 
                                       required>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="amount" class="form-label">Valor <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount', $revenue->amount) }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="date" class="form-label">Data <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('date') is-invalid @enderror" 
                                       id="date" 
                                       name="date" 
                                       value="{{ old('date', $revenue->date->format('Y-m-d')) }}" 
                                       lang="pt-BR"
                                       required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="fonte_id" class="form-label">Fonte <span class="text-danger">*</span></label>
                                <select class="form-select @error('fonte_id') is-invalid @enderror" 
                                        id="fonte_id" 
                                        name="fonte_id" 
                                        required>
                                    <option value="">Selecione uma fonte</option>
                                    @foreach($fontes as $fonte)
                                        <option value="{{ $fonte->id }}" {{ old('fonte_id', $revenue->fonte_id) == $fonte->id ? 'selected' : '' }}>
                                            {{ $fonte->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fonte_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="bloco_id" class="form-label">Bloco <span class="text-danger">*</span></label>
                                <select class="form-select @error('bloco_id') is-invalid @enderror" 
                                        id="bloco_id" 
                                        name="bloco_id" 
                                        required>
                                    <option value="">Selecione um bloco</option>
                                    @foreach($blocos as $bloco)
                                        <option value="{{ $bloco->id }}" {{ old('bloco_id', $revenue->bloco_id) == $bloco->id ? 'selected' : '' }}>
                                            {{ $bloco->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bloco_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="grupo_id" class="form-label">Grupo <span class="text-danger">*</span></label>
                                <select class="form-select @error('grupo_id') is-invalid @enderror" 
                                        id="grupo_id" 
                                        name="grupo_id" 
                                        required>
                                    <option value="">Selecione um grupo</option>
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}" {{ old('grupo_id', $revenue->grupo_id) == $grupo->id ? 'selected' : '' }}>
                                            {{ $grupo->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('grupo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="acao_id" class="form-label">Ação <span class="text-danger">*</span></label>
                                <select class="form-select @error('acao_id') is-invalid @enderror" 
                                        id="acao_id" 
                                        name="acao_id" 
                                        required>
                                    <option value="">Selecione uma ação</option>
                                    @foreach($acoes as $acao)
                                        <option value="{{ $acao->id }}" {{ old('acao_id', $revenue->acao_id) == $acao->id ? 'selected' : '' }}>
                                            {{ $acao->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('acao_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observation" class="form-label">Observação</label>
                            <textarea class="form-control @error('observation') is-invalid @enderror" 
                                      id="observation" 
                                      name="observation" 
                                      rows="3">{{ old('observation', $revenue->observation) }}</textarea>
                            @error('observation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
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
    // Função para carregar os blocos
    function loadBlocos(fonteId) {
        if (!fonteId) {
            document.getElementById('bloco_id').innerHTML = '<option value="">Selecione um bloco</option>';
            document.getElementById('bloco_id').disabled = true;
            return;
        }

        fetch(`{{ url('revenues/categories/blocos') }}/${fonteId}`)
            .then(response => response.json())
            .then(blocos => {
                let html = '<option value="">Selecione um bloco</option>';
                blocos.forEach(bloco => {
                    const selected = bloco.id == {{ old('bloco_id', $revenue->bloco_id) }} ? 'selected' : '';
                    html += `<option value="${bloco.id}" ${selected}>${bloco.name}</option>`;
                });
                document.getElementById('bloco_id').innerHTML = html;
                document.getElementById('bloco_id').disabled = false;
            });
    }

    // Função para carregar os grupos
    function loadGrupos(blocoId) {
        if (!blocoId) {
            document.getElementById('grupo_id').innerHTML = '<option value="">Selecione um grupo</option>';
            document.getElementById('grupo_id').disabled = true;
            return;
        }

        fetch(`{{ url('revenues/categories/grupos') }}/${blocoId}`)
            .then(response => response.json())
            .then(grupos => {
                let html = '<option value="">Selecione um grupo</option>';
                grupos.forEach(grupo => {
                    const selected = grupo.id == {{ old('grupo_id', $revenue->grupo_id) }} ? 'selected' : '';
                    html += `<option value="${grupo.id}" ${selected}>${grupo.name}</option>`;
                });
                document.getElementById('grupo_id').innerHTML = html;
                document.getElementById('grupo_id').disabled = false;
            });
    }

    // Função para carregar as ações
    function loadAcoes(grupoId) {
        if (!grupoId) {
            document.getElementById('acao_id').innerHTML = '<option value="">Selecione uma ação</option>';
            document.getElementById('acao_id').disabled = true;
            return;
        }

        fetch(`{{ url('revenues/categories/acoes') }}/${grupoId}`)
            .then(response => response.json())
            .then(acoes => {
                let html = '<option value="">Selecione uma ação</option>';
                acoes.forEach(acao => {
                    const selected = acao.id == {{ old('acao_id', $revenue->acao_id) }} ? 'selected' : '';
                    html += `<option value="${acao.id}" ${selected}>${acao.name}</option>`;
                });
                document.getElementById('acao_id').innerHTML = html;
                document.getElementById('acao_id').disabled = false;
            });
    }

    // Event Listeners
    document.getElementById('fonte_id').addEventListener('change', function() {
        loadBlocos(this.value);
        document.getElementById('grupo_id').innerHTML = '<option value="">Selecione um grupo</option>';
        document.getElementById('grupo_id').disabled = true;
        document.getElementById('acao_id').innerHTML = '<option value="">Selecione uma ação</option>';
        document.getElementById('acao_id').disabled = true;
    });

    document.getElementById('bloco_id').addEventListener('change', function() {
        loadGrupos(this.value);
        document.getElementById('acao_id').innerHTML = '<option value="">Selecione uma ação</option>';
        document.getElementById('acao_id').disabled = true;
    });

    document.getElementById('grupo_id').addEventListener('change', function() {
        loadAcoes(this.value);
    });

    // Carregar dados iniciais
    const fonteId = document.getElementById('fonte_id').value;
    if (fonteId) {
        loadBlocos(fonteId);
        const blocoId = {{ old('bloco_id', $revenue->bloco_id) }};
        if (blocoId) {
            loadGrupos(blocoId);
            const grupoId = {{ old('grupo_id', $revenue->grupo_id) }};
            if (grupoId) {
                loadAcoes(grupoId);
            }
        }
    }
});
</script>
@endpush
@endsection
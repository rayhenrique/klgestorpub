@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Nova Receita</h1>
                <a href="{{ route('revenues.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('revenues.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('description') is-invalid @enderror" 
                                       id="description" 
                                       name="description" 
                                       value="{{ old('description') }}" 
                                       required>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 mb-3">
                                <label for="amount" class="form-label">Valor <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount') }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 mb-3">
                                <label for="date" class="form-label">Data <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('date') is-invalid @enderror" 
                                       id="date" 
                                       name="date" 
                                       value="{{ old('date') }}" 
                                       lang="pt-BR"
                                       required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                                <label for="fonte_id" class="form-label">Fonte <span class="text-danger">*</span></label>
                                <select class="form-select @error('fonte_id') is-invalid @enderror" 
                                        id="fonte_id" 
                                        name="fonte_id" 
                                        required>
                                    <option value="">Selecione uma fonte</option>
                                    @foreach($fontes as $fonte)
                                        <option value="{{ $fonte->id }}" {{ old('fonte_id') == $fonte->id ? 'selected' : '' }}>
                                            {{ $fonte->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fonte_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                                <label for="bloco_id" class="form-label">Bloco <span class="text-danger">*</span></label>
                                <select class="form-select @error('bloco_id') is-invalid @enderror" 
                                        id="bloco_id" 
                                        name="bloco_id" 
                                        required 
                                        disabled>
                                    <option value="">Selecione um bloco</option>
                                </select>
                                @error('bloco_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                                <label for="grupo_id" class="form-label">Grupo <span class="text-danger">*</span></label>
                                <select class="form-select @error('grupo_id') is-invalid @enderror" 
                                        id="grupo_id" 
                                        name="grupo_id" 
                                        required 
                                        disabled>
                                    <option value="">Selecione um grupo</option>
                                </select>
                                @error('grupo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 mb-3">
                                <label for="acao_id" class="form-label">Ação <span class="text-danger">*</span></label>
                                <select class="form-select @error('acao_id') is-invalid @enderror" 
                                        id="acao_id" 
                                        name="acao_id" 
                                        required 
                                        disabled>
                                    <option value="">Selecione uma ação</option>
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
                                      rows="3">{{ old('observation') }}</textarea>
                            @error('observation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('revenues.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salvar
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
                    html += `<option value="${bloco.id}">${bloco.name}</option>`;
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
                    html += `<option value="${grupo.id}">${grupo.name}</option>`;
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
                    html += `<option value="${acao.id}">${acao.name}</option>`;
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

    // Carregar dados iniciais se houver
    const fonteId = document.getElementById('fonte_id').value;
    if (fonteId) {
        loadBlocos(fonteId);
    }
});
</script>
@endpush
@endsection
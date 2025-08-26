@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Editar Categoria</h1>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Código</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $category->code) }}">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Tipo</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Selecione...</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}" {{ old('type', $category->type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="parent_id" class="form-label">Categoria Pai</label>
                                <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                    <option value="">Selecione...</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', $category->active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">Ativo</label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
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
    const typeSelect = document.getElementById('type');
    const parentSelect = document.getElementById('parent_id');
    const currentParentId = '{{ $category->parent_id }}';

    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        // Limpa o select de pai
        parentSelect.innerHTML = '<option value="">Selecione...</option>';
        
        if (!selectedType) {
            parentSelect.disabled = true;
            return;
        }

        // Se for fonte, desabilita o select de pai
        if (selectedType === 'fonte') {
            parentSelect.disabled = true;
            return;
        }

        // Habilita o select de pai
        parentSelect.disabled = false;

        // Busca os pais disponíveis via AJAX
        fetch(`{{ route('categories.parents.available') }}?type=${selectedType}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(parent => {
                    const option = new Option(parent.name, parent.id);
                    if (parent.id == currentParentId) {
                        option.selected = true;
                    }
                    parentSelect.add(option);
                });
            })
            .catch(error => console.error('Erro ao carregar categorias pai:', error));
    });

    // Dispara o evento change se já houver um tipo selecionado
    if (typeSelect.value) {
        typeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush

@endsection 
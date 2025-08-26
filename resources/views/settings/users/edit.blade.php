@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Editar Usuário</h1>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password">
                                <small class="text-muted">Deixe em branco para manter a senha atual</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" 
                                    id="password_confirmation" name="password_confirmation">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Função</label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                    <option value="operator" {{ old('role', $user->role) === 'operator' ? 'selected' : '' }}>Operador</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" 
                                        id="active" name="active" value="1" 
                                        {{ old('active', $user->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">Usuário Ativo</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection 
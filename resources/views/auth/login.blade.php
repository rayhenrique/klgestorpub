@extends('layouts.app')

@section('content')
<div class="container login-container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="company-logo">
                <div class="logo-circle">
                    <i class="fas fa-landmark"></i>
                </div>
                <h4>Sistema de Gestão<br><span>de Contas Públicas</span></h4>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-shield-alt me-2"></i>{{ __('Portal de Acesso') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-user-tie me-2"></i>{{ __('Email Institucional') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                                <input id="email" type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" 
                                    required autocomplete="email" autofocus
                                    placeholder="seu.email@empresa.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-key me-2"></i>{{ __('Senha') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="current-password"
                                    placeholder="Digite sua senha">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" 
                                    id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    <i class="fas fa-check-circle me-2"></i>{{ __('Manter conectado') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-door-open me-2"></i>{{ __('Acessar Sistema') }}
                            </button>

                            @if (Route::has('password.reset'))
                                <div class="text-center mt-3">
                                    <a class="btn-link" href="{{ route('password.request') }}">
                                        <i class="fas fa-question-circle me-2"></i>{{ __('Esqueceu sua senha?') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-balance-scale me-2"></i>Sistema em conformidade com a Lei de Responsabilidade Fiscal
                    <br><i class="far fa-copyright"></i> {{ date('Y') }} {{ config('app.name') }} - v{{ config('app.version') }}
                    <br>Desenvolvido por <a href="https://kltecnologia.com" target="_blank" class="text-muted text-decoration-none">KL Tecnologia</a>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
@endpush

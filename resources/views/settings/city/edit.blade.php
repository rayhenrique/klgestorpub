@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dados da Prefeitura</h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('settings.city.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city_name" class="form-label">Nome da Cidade</label>
                                <input type="text" class="form-control @error('city_name') is-invalid @enderror" 
                                    id="city_name" name="city_name" value="{{ old('city_name', $settings->city_name) }}">
                                @error('city_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="city_hall_name" class="form-label">Secretaria</label>
                                <input type="text" class="form-control @error('city_hall_name') is-invalid @enderror" 
                                    id="city_hall_name" name="city_hall_name" value="{{ old('city_hall_name', $settings->city_hall_name) }}">
                                @error('city_hall_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                    id="address" name="address" value="{{ old('address', $settings->address) }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="zip_code" class="form-label">CEP</label>
                                <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                    id="zip_code" name="zip_code" value="{{ old('zip_code', $settings->zip_code) }}">
                                @error('zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="state" class="form-label">UF</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                    id="state" name="state" value="{{ old('state', $settings->state) }}" maxlength="2">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="ibge_code" class="form-label">Código IBGE</label>
                                <input type="text" class="form-control @error('ibge_code') is-invalid @enderror" 
                                    id="ibge_code" name="ibge_code" value="{{ old('ibge_code', $settings->ibge_code) }}">
                                @error('ibge_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="mayor_name" class="form-label">Nome do Prefeito</label>
                                <input type="text" class="form-control @error('mayor_name') is-invalid @enderror" 
                                    id="mayor_name" name="mayor_name" value="{{ old('mayor_name', $settings->mayor_name) }}">
                                @error('mayor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                    id="phone" name="phone" value="{{ old('phone', $settings->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" value="{{ old('email', $settings->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Máscara para CEP
    const zipCode = document.getElementById('zip_code');
    zipCode.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 8) value = value.slice(0, 8);
        if (value.length === 8) {
            value = value.slice(0, 5) + '-' + value.slice(5);
        }
        e.target.value = value;
    });

    // Validação do CEP no envio do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        const zipValue = zipCode.value.replace(/\D/g, '');
        if (zipValue.length > 0 && zipValue.length !== 8) {
            e.preventDefault();
            alert('O CEP deve ter exatamente 8 dígitos');
        }
    });

    // Máscara para telefone
    const phone = document.getElementById('phone');
    phone.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        if (value.length > 2) value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
        if (value.length > 9) value = value.slice(0, 9) + '-' + value.slice(9);
        e.target.value = value;
    });

    // Converter UF para maiúsculo
    const state = document.getElementById('state');
    state.addEventListener('input', function (e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endpush 
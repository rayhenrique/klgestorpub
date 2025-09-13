<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - Sistema de Gestão de Contas Públicas</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column h-100">
    <div id="app" class="flex-shrink-0">
        @include('components.flash-messages')
        
        @if (!Route::is('login') && !Route::is('register') && !Route::is('password.*'))
            <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
                <div class="container-fluid">
                    <!-- Mobile Sidebar Toggle -->
                    <button class="btn btn-outline-light d-md-none me-2" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                        <div class="brand-icon me-2">
                            <i class="fas fa-landmark"></i>
                        </div>
                        <span class="d-none d-sm-inline">{{ config('app.name') }}</span>
                        <span class="d-sm-none">KL Gestor</span>
                    </a>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto">
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto align-items-center">
                            @auth
                                <li class="nav-item me-3">
                                    <a class="nav-link" href="#" title="Notificações">
                                        <i class="fas fa-bell"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            3
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        <div class="avatar-circle bg-white bg-opacity-25 me-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-circle me-2"></i>Perfil
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        @if(Auth::user()->isAdmin())
                                            <h6 class="dropdown-header">Administração</h6>
                                            <a class="dropdown-item" href="{{ route('users.index') }}">
                                                <i class="fas fa-users me-2"></i>Usuários
                                            </a>
                                            <a class="dropdown-item" href="{{ route('settings.city.edit') }}">
                                                <i class="fas fa-city me-2"></i>Dados da Prefeitura
                                            </a>
                                            <div class="dropdown-divider"></div>
                                        @endif
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>{{ __('Sair') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
        @endif

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    @if (!Route::is('login') && !Route::is('register') && !Route::is('password.*'))
        @include('layouts.footer')
    @endif
    
    @stack('scripts')

    <script>
        // Função para mostrar notificação de sucesso
        function showSuccess(message) {
            Swal.fire({
                title: 'Sucesso!',
                text: message,
                icon: 'success',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Se houver mensagem de sucesso na sessão, mostra a notificação
        @if(session('success'))
            showSuccess("{{ session('success') }}");
        @endif

        function confirmDelete(event, message) {
            event.preventDefault();
            const form = event.target.closest('form');
            
            Swal.fire({
                title: 'Confirmação de Exclusão',
                text: message || 'Tem certeza que deseja excluir este item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmEdit(event, message) {
            event.preventDefault();
            const link = event.currentTarget.href;
            
            Swal.fire({
                title: 'Confirmação de Edição',
                text: message || 'Deseja editar este item?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, editar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            });
        }

        function confirmAction(event, message, title = 'Confirmação') {
            event.preventDefault();
            const form = event.target.closest('form');
            
            Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebarMenu');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            if (sidebarToggle && sidebar && sidebarOverlay) {
                // Toggle sidebar
                function toggleSidebar() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                    body.classList.toggle('sidebar-open');
                }

                // Close sidebar
                function closeSidebar() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    body.classList.remove('sidebar-open');
                }

                // Event listeners
                sidebarToggle.addEventListener('click', toggleSidebar);
                sidebarOverlay.addEventListener('click', closeSidebar);

                // Close sidebar on window resize if screen becomes larger
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
                        closeSidebar();
                    }
                });

                // Close sidebar on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                        closeSidebar();
                    }
                });
            }
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Sistema de Gestão de Contas Públicas</title>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            min-height: 600px;
        }
        
        .feature-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 0.75rem;
            background-color: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .testimonial-card {
            background-color: #f8fafc;
            border-radius: 1rem;
            padding: 2rem;
            margin: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section text-white py-5">
        <div class="container">
            <nav class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ url('/') }}" class="text-white text-decoration-none fs-4">
                    <i class="fas fa-landmark me-2"></i>
                    {{ config('app.name') }}
                </a>
                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-light">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Login</a>
                            <a href="https://wa.me/5582996304742?text=Gostaria%20de%20ver%20uma%20demonstra%C3%A7%C3%A3o%20do%20Sistema%20KL%20Gestor%20Pub" class="btn btn-light" target="_blank">Demonstração</a>
                        @endif
                    @endif
                </div>
            </nav>
            
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Gestão de Contas Públicas Simplificada</h1>
                    <p class="lead mb-4">Uma solução completa para administração e controle financeiro de órgãos públicos, oferecendo transparência, eficiência e conformidade.</p>
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg me-2">Começar Agora</a>
                    <a href="https://wa.me/5582996304742?text=Gostaria%20de%20ver%20uma%20demonstra%C3%A7%C3%A3o%20do%20Sistema%20KL%20Gestor%20Pub" class="btn btn-outline-light btn-lg" target="_blank">
                        <i class="fab fa-whatsapp me-2"></i>Solicitar Demonstração
                    </a>
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('images/dashboard-preview.svg') }}" alt="Dashboard Preview" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Recursos Principais</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Controle Financeiro</h3>
                        <p>Gerencie despesas e receitas com facilidade, mantendo o controle total sobre as finanças públicas.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3>Relatórios Detalhados</h3>
                        <p>Gere relatórios completos e personalizados para uma visão clara da situação financeira.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Conformidade Legal</h3>
                        <p>Mantenha-se em conformidade com as legislações e normas contábeis do setor público.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Por que escolher o {{ config('app.name') }}?</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="fas fa-check-circle text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h4>Interface Intuitiva</h4>
                            <p>Design moderno e fácil de usar, permitindo que você se concentre no que realmente importa.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="fas fa-lock text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h4>Segurança Avançada</h4>
                            <p>Seus dados estão protegidos com as mais recentes tecnologias de segurança.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="fas fa-sync text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h4>Atualizações Constantes</h4>
                            <p>Sistema sempre atualizado com as últimas mudanças na legislação.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="fas fa-headset text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h4>Suporte Especializado</h4>
                            <p>Equipe de suporte técnico pronta para ajudar quando você precisar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5">
        <div class="container text-center">
            <h2 class="mb-4">Pronto para começar?</h2>
            <p class="lead mb-4">Simplifique a gestão das contas públicas do seu município hoje mesmo.</p>
            <a href="https://wa.me/5582996304742?text=Gostaria%20de%20ver%20uma%20demonstra%C3%A7%C3%A3o%20do%20Sistema%20KL%20Gestor%20Pub" class="btn btn-primary btn-lg" target="_blank">
                <i class="fab fa-whatsapp me-2"></i>Solicitar Demonstração
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ config('app.name') }}</h5>
                    <p>Sistema de Gestão de Contas Públicas</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>Contato: <a href="mailto:rayhenrique@gmail.com" class="text-white">rayhenrique@gmail.com</a></p>
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

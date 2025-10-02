<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="KL Gestor Pub - Sistema de Gestão de Contas Públicas. Uma solução completa para administração e controle financeiro de órgãos públicos.">
    <meta name="theme-color" content="#1e3a8a">
    
    <title>{{ config('app.name') }} - Sistema de Gestão de Contas Públicas</title>
    
    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Assets are now bundled via Vite - FontAwesome imported in app.js -->
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            --transition-base: all 0.3s ease;
        }

        body {
            font-family: 'Nunito', sans-serif;
            scroll-behavior: smooth;
        }

        .hero-section {
            background: var(--primary-gradient);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to bottom right, transparent 49%, white 50%);
        }
        
        .feature-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 1rem;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition-base);
        }

        .feature-card {
            padding: 2rem;
            border-radius: 1rem;
            transition: var(--transition-base);
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1);
        }

        .benefit-icon {
            transition: var(--transition-base);
        }

        .benefit-card {
            padding: 1.5rem;
            border-radius: 1rem;
            transition: var(--transition-base);
        }

        .benefit-card:hover {
            background: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .benefit-card:hover .benefit-icon {
            transform: scale(1.1);
            color: #3b82f6;
        }

        .cta-section {
            background: var(--primary-gradient);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to top right, transparent 49%, white 50%);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            transition: var(--transition-base);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59,130,246,0.4);
        }

        .btn-outline-light {
            transition: var(--transition-base);
        }

        .btn-outline-light:hover {
            transform: translateY(-2px);
        }

        @media (max-width: 767.98px) {
            .hero-section {
                min-height: auto;
                padding-top: 6rem;
                padding-bottom: 8rem;
            }

            .display-4 {
                font-size: 2.5rem;
            }

            .hero-section::after {
                height: 50px;
            }

            .cta-section::before {
                height: 50px;
            }

            .feature-icon {
                width: 3.5rem;
                height: 3.5rem;
                font-size: 1.25rem;
            }
        }

        @media (max-width: 575.98px) {
            .btn-group-lg > .btn, .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
        }

        .scroll-down {
            position: absolute;
            bottom: 120px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            text-align: center;
            z-index: 1;
        }

        .scroll-down i {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section text-white py-5">
        <div class="container">
            <nav class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ url('/') }}" class="text-white text-decoration-none fs-4 d-flex align-items-center">
                    <i class="fas fa-landmark me-2"></i>
                    <span>{{ config('app.name') }}</span>
                </a>
                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-light">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light me-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        @endif
                    @endif
                </div>
            </nav>
            
            <div class="row align-items-center gy-5">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Gestão de Contas Públicas Simplificada</h1>
                    <p class="lead mb-4">Uma solução completa para administração e controle financeiro de órgãos públicos, oferecendo transparência, eficiência e conformidade.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-rocket me-2"></i>Começar Agora
                        </a>
                        <a href="https://wa.me/5582996304742?text=Gostaria%20de%20ver%20uma%20demonstra%C3%A7%C3%A3o%20do%20Sistema%20KL%20Gestor%20Pub" 
                           class="btn btn-outline-light btn-lg"
                           target="_blank"
                           rel="noopener">
                            <i class="fab fa-whatsapp me-2"></i>Solicitar Demonstração
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('images/dashboard-preview.svg') }}" 
                         alt="Preview do Dashboard do KL Gestor Pub" 
                         class="img-fluid rounded-4 shadow-lg"
                         width="600"
                         height="400"
                         loading="lazy">
                </div>
            </div>

            <div class="scroll-down d-none d-lg-block">
                <p class="mb-2">Saiba mais</p>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Recursos Principais</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="text-center">
                            <div class="feature-icon mx-auto">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="h4">Controle Financeiro</h3>
                            <p class="text-muted">Gerencie despesas e receitas com facilidade, mantendo o controle total sobre as finanças públicas.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="text-center">
                            <div class="feature-icon mx-auto">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h3 class="h4">Relatórios Detalhados</h3>
                            <p class="text-muted">Gere relatórios completos e personalizados para uma visão clara da situação financeira.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="text-center">
                            <div class="feature-icon mx-auto">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3 class="h4">Conformidade Legal</h3>
                            <p class="text-muted">Mantenha-se em conformidade com as legislações e normas contábeis do setor público.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Por que escolher o {{ config('app.name') }}?</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="benefit-card">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-check-circle benefit-icon text-primary fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="h5">Interface Intuitiva</h4>
                                <p class="text-muted mb-0">Design moderno e fácil de usar, permitindo que você se concentre no que realmente importa.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="benefit-card">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-lock benefit-icon text-primary fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="h5">Segurança Avançada</h4>
                                <p class="text-muted mb-0">Seus dados estão protegidos com as mais recentes tecnologias de segurança.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="benefit-card">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-sync benefit-icon text-primary fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="h5">Atualizações Constantes</h4>
                                <p class="text-muted mb-0">Sistema sempre atualizado com as últimas mudanças na legislação.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="benefit-card">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-headset benefit-icon text-primary fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="h5">Suporte Especializado</h4>
                                <p class="text-muted mb-0">Equipe de suporte técnico pronta para ajudar quando você precisar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section text-white py-5">
        <div class="container text-center py-5">
            <h2 class="mb-4">Pronto para começar?</h2>
            <p class="lead mb-4">Simplifique a gestão das contas públicas do seu município hoje mesmo.</p>
            <a href="https://wa.me/5582996304742?text=Gostaria%20de%20ver%20uma%20demonstra%C3%A7%C3%A3o%20do%20Sistema%20KL%20Gestor%20Pub" 
               class="btn btn-light btn-lg"
               target="_blank"
               rel="noopener">
                <i class="fab fa-whatsapp me-2"></i>Solicitar Demonstração
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-landmark me-2"></i>
                        <h5 class="mb-0">{{ config('app.name') }}</h5>
                    </div>
                    <p class="mb-0">Sistema de Gestão de Contas Públicas</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:rayhenrique@gmail.com" class="text-white text-decoration-none">rayhenrique@gmail.com</a>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-copyright me-2"></i>
                        {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
            id="backToTop" 
            class="btn btn-primary rounded-circle position-fixed bottom-0 end-0 m-4"
            style="display: none; z-index: 1000;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Back to Top Button
        window.onscroll = function() {
            const btn = document.getElementById('backToTop');
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                btn.style.display = 'block';
            } else {
                btn.style.display = 'none';
            }
        };
    </script>
</body>
</html>

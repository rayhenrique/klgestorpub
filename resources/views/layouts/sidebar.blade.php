<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::is('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="fas fa-sitemap me-2"></i>
                    Categorias
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::is('expense-classifications.*') ? 'active' : '' }}" href="{{ route('expense-classifications.index') }}">
                    <i class="fas fa-tags me-2"></i>
                    Classificação de Despesas
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::is('revenues.*') ? 'active' : '' }}" href="{{ route('revenues.index') }}">
                    <i class="fas fa-hand-holding-usd me-2"></i>
                    Receitas
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::is('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                    <i class="fas fa-money-bill-alt me-2"></i>
                    Despesas
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Route::is('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <i class="fas fa-chart-bar me-2"></i>
                    Relatórios
                </a>
            </li>

            <!-- Documentação -->
            <li class="nav-item">
                <a class="nav-link {{ Route::is('documentation.manual') ? 'active' : '' }}" href="{{ route('documentation.manual') }}">
                    <i class="fas fa-book me-2"></i>
                    Manual do Usuário
                </a>
            </li>

            <!-- Documentos Legais -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#legalDocs" aria-expanded="false">
                    <i class="fas fa-gavel me-2"></i>
                    Documentos Legais
                    <i class="fas fa-chevron-down ms-2"></i>
                </a>
                <div class="collapse" id="legalDocs">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('documentation.privacy') ? 'active' : '' }}" href="{{ route('documentation.privacy') }}">
                                <i class="fas fa-shield-alt me-2"></i>
                                Política de Privacidade
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('documentation.terms') ? 'active' : '' }}" href="{{ route('documentation.terms') }}">
                                <i class="fas fa-file-contract me-2"></i>
                                Termos de Uso
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('documentation.lgpd') ? 'active' : '' }}" href="{{ route('documentation.lgpd') }}">
                                <i class="fas fa-user-shield me-2"></i>
                                LGPD
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            @auth
                @php
                    \Log::info('User check', [
                        'user' => auth()->user(),
                        'is_admin' => auth()->user()->isAdmin()
                    ]);
                @endphp

                @if(auth()->user()->isAdmin())
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Administração</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-users me-2"></i>
                                Usuários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('settings.city.*') ? 'active' : '' }}" href="{{ route('settings.city.edit') }}">
                                <i class="fas fa-city me-2"></i>
                                Dados da Prefeitura
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                                <i class="fas fa-history me-2"></i>
                                Logs de Auditoria
                            </a>
                        </li>
                    </ul>
                @endif
            @endauth
        </ul>
    </div>
</div> 
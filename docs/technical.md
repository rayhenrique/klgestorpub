# 📋 Documentação Técnica - KL Gestor Pub v1.4.0

## 🏗️ Arquitetura do Sistema

### Padrão MVC Aprimorado
O sistema segue o padrão MVC (Model-View-Controller) do Laravel 11.38.2, com arquitetura moderna e otimizada:

- **Models**: Entidades especializadas com relacionamentos otimizados (Revenue, Expense, Category)
- **Views**: Templates Blade responsivos com componentes reutilizáveis
- **Controllers**: Lógica de negócio limpa com validação robusta
- **Services**: Camada de serviços para lógica complexa (ReportService)
- **Traits**: Funcionalidades compartilhadas (Auditable)
- **Requests**: Validação especializada (StoreRevenueRequest, UpdateExpenseRequest)

### Estrutura de Diretórios Reorganizada (v1.4.0)

```
klgestorpub/
├── app/                    # Código da aplicação Laravel
│   ├── Console/           # Comandos Artisan (backup:database, backup:restore)
│   ├── Exports/           # Classes para exportação Excel/PDF
│   ├── Http/
│   │   ├── Controllers/   # Controllers especializados
│   │   ├── Middleware/    # Middlewares de autenticação
│   │   └── Requests/      # Form Request classes para validação
│   ├── Models/            # Models Eloquent otimizados
│   ├── Services/          # Camada de serviços (ReportService)
│   └── Traits/           # Traits compartilhados (Auditable)
    
resources/
├── views/                 # Templates Blade responsivos
│   ├── layouts/          # Layouts base com sidebar responsivo
│   ├── components/       # Componentes reutilizáveis
│   ├── auth/            # Views de autenticação
│   ├── settings/        # Views de configurações + backup
│   ├── categories/      # CRUD de categorias
│   ├── revenues/        # CRUD de receitas
│   ├── expenses/        # CRUD de despesas
│   └── reports/         # Relatórios e dashboards
├── css/                  # Arquivos CSS
├── js/                   # Scripts JavaScript
└── sass/                 # Arquivos SASS para compilação
    
database/
├── migrations/           # Migrações especializadas (revenues/expenses)
├── seeders/             # Seeders para dados iniciais
└── factories/           # Factories para testes

deployment/              # Scripts e configurações de deploy
├── docker/              # Configurações Docker completas
├── *.sh                 # Scripts de instalação e deploy
└── nginx-*.conf         # Configurações Nginx

docs/                    # Documentação completa
├── DOCKER.md           # Guia Docker
├── MANUAL.md           # Manual do usuário
├── BACKUP.md           # Sistema de backup
└── technical.md        # Documentação técnica

infrastructure/         # Arquivos de infraestrutura
├── logs/               # Logs externos
├── secrets/            # Arquivos sensíveis
└── volumes/            # Volumes Docker

public/
├── build/              # Assets compilados (Vite)
│   └── assets/         # CSS/JS otimizados
└── images/             # Imagens e ícones

tests/                  # Testes automatizados
├── Feature/            # Testes funcionais
└── Unit/               # Testes unitários
```

## 🗄️ Banco de Dados (Arquitetura v1.4.0)

### Diagrama ER Otimizado
```
users                    # Usuários do sistema
├── id (PK)
├── name
├── email (unique)
├── email_verified_at
├── password
├── role (admin/operator)
├── remember_token
├── created_at
└── updated_at

categories              # Sistema hierárquico de categorias
├── id (PK)
├── name
├── code
├── type (fonte/bloco/grupo/acao)
├── parent_id (FK → categories.id)
├── active (boolean)
├── created_at
└── updated_at

revenues                # Receitas (tabela especializada)
├── id (PK)
├── description
├── amount (decimal 15,2)
├── date
├── fonte_id (FK → categories.id)
├── bloco_id (FK → categories.id)
├── grupo_id (FK → categories.id)
├── acao_id (FK → categories.id)
├── observation (text)
├── created_at
└── updated_at

expenses                # Despesas (tabela especializada)
├── id (PK)
├── description
├── amount (decimal 15,2)
├── date
├── fonte_id (FK → categories.id)
├── bloco_id (FK → categories.id)
├── grupo_id (FK → categories.id)
├── acao_id (FK → categories.id)
├── classification_id (FK → expense_classifications.id)
├── observation (text)
├── created_at
└── updated_at

expense_classifications # Classificações de despesas
├── id (PK)
├── name
├── active (boolean)
├── created_at
└── updated_at

city_settings          # Configurações municipais
├── id (PK)
├── city_name
├── city_hall_name
├── address
├── ibge_code
├── state
├── zip_code
├── phone
├── email
├── mayor_name
├── created_at
└── updated_at

audit_logs             # Logs de auditoria
├── id (PK)
├── user_id (FK → users.id)
├── action (created/updated/deleted)
├── model_type
├── model_id
├── old_values (json)
├── new_values (json)
└── created_at

cache                  # Cache do Laravel
├── key (PK)
├── value (longtext)
└── expiration (int)

jobs                   # Filas de trabalho
├── id (PK)
├── queue
├── payload (longtext)
├── attempts (tinyint)
├── reserved_at (int)
├── available_at (int)
└── created_at (int)

job_batches           # Lotes de jobs
├── id (PK)
├── name
├── total_jobs (int)
├── pending_jobs (int)
├── failed_jobs (int)
├── failed_job_ids (longtext)
├── options (mediumtext)
├── cancelled_at (int)
├── created_at (int)
└── finished_at (int)

failed_jobs           # Jobs que falharam
├── id (PK)
├── uuid (unique)
├── connection (text)
├── queue (text)
├── payload (longtext)
├── exception (longtext)
└── failed_at (timestamp)
```

### **Melhorias na Arquitetura v1.4.0:**
- ✅ **Tabelas Especializadas**: Separação clara entre `revenues` e `expenses`
- ✅ **Performance Otimizada**: Índices e relacionamentos otimizados
- ✅ **Integridade Referencial**: Foreign keys bem definidas
- ✅ **Escalabilidade**: Estrutura preparada para crescimento
- ✅ **Auditoria Completa**: Rastreamento de todas as operações

### Relacionamentos

- **Category**:
  - `belongsTo` self (parent)
  - `hasMany` self (children)
  - `hasMany` Revenue (fonte/bloco/grupo/acao)
  - `hasMany` Expense (fonte/bloco/grupo/acao)

- **Revenue**:
  - `belongsTo` Category (fonte)
  - `belongsTo` Category (bloco)
  - `belongsTo` Category (grupo)
  - `belongsTo` Category (acao)

- **Expense**:
  - `belongsTo` Category (fonte)
  - `belongsTo` Category (bloco)
  - `belongsTo` Category (grupo)
  - `belongsTo` Category (acao)
  - `belongsTo` ExpenseClassification

## 🔐 Autenticação e Autorização (v1.4.0)

### Middleware Aprimorado

- **auth**: Verifica autenticação com sessões seguras
- **admin**: Controle de acesso administrativo
- **operator**: Permissões limitadas para operadores
- **throttle**: Rate limiting para proteção contra ataques
- **verified**: Verificação de email (se habilitada)

### Sistema de Roles

- **Admin**: 
  - ✅ Acesso total ao sistema
  - ✅ Gerenciamento de usuários
  - ✅ Configurações do sistema
  - ✅ Backup e restauração
  - ✅ Logs de auditoria
  - ✅ Relatórios avançados

- **Operator**: 
  - ✅ CRUD de receitas e despesas
  - ✅ Visualização de relatórios
  - ✅ Dashboard básico
  - ❌ Criação/edição de categorias
  - ❌ Configurações do sistema
  - ❌ Gerenciamento de usuários

### Segurança Implementada

- **CSRF Protection**: Tokens em todos os formulários
- **Password Hashing**: Bcrypt com salt automático
- **Session Security**: Configurações seguras de sessão
- **Rate Limiting**: Proteção contra força bruta
- **Input Validation**: Sanitização de todas as entradas
- **SQL Injection Protection**: Eloquent ORM com prepared statements

## 🌐 APIs e Endpoints (v1.4.0)

### Autenticação
```
GET    /login                  # Página de login
POST   /login                  # Processar login
POST   /logout                 # Logout do usuário
GET    /register               # Página de registro (se habilitado)
POST   /register               # Processar registro
```

### Dashboard
```
GET    /                       # Dashboard principal
GET    /dashboard              # Dashboard alternativo
GET    /home                   # Página inicial
```

### Categorias (Admin apenas)
```
GET    /categories             # Lista categorias
GET    /categories/create      # Formulário de criação
POST   /categories             # Cria categoria
GET    /categories/{id}        # Visualiza categoria
GET    /categories/{id}/edit   # Formulário de edição
PUT    /categories/{id}        # Atualiza categoria
DELETE /categories/{id}        # Remove categoria
GET    /categories/{id}/children # Obtém subcategorias (AJAX)
```

### Receitas
```
GET    /revenues               # Lista receitas
GET    /revenues/create        # Formulário de criação
POST   /revenues               # Cria receita
GET    /revenues/{id}          # Visualiza receita
GET    /revenues/{id}/edit     # Formulário de edição
PUT    /revenues/{id}          # Atualiza receita
DELETE /revenues/{id}          # Remove receita
```

### Despesas
```
GET    /expenses               # Lista despesas
GET    /expenses/create        # Formulário de criação
POST   /expenses               # Cria despesa
GET    /expenses/{id}          # Visualiza despesa
GET    /expenses/{id}/edit     # Formulário de edição
PUT    /expenses/{id}          # Atualiza despesa
DELETE /expenses/{id}          # Remove despesa
```

### Classificações de Despesas (Admin apenas)
```
GET    /expense-classifications # Lista classificações
GET    /expense-classifications/create # Formulário de criação
POST   /expense-classifications # Cria classificação
GET    /expense-classifications/{id}/edit # Formulário de edição
PUT    /expense-classifications/{id} # Atualiza classificação
DELETE /expense-classifications/{id} # Remove classificação
```

### Relatórios
```
GET    /reports                # Página de relatórios
POST   /reports/generate       # Gera relatório customizado
GET    /reports/revenues       # Relatório de receitas
GET    /reports/expenses       # Relatório de despesas
GET    /reports/balance        # Relatório de balanço
GET    /reports/export/{type}  # Exporta relatório (PDF/Excel)
```

### Sistema de Backup (Admin apenas)
```
GET    /settings/backup        # Página de backup
POST   /settings/backup/create # Criar backup
GET    /settings/backup/download/{filename} # Download backup
POST   /settings/backup/upload # Upload backup para restauração
POST   /settings/backup/restore # Restaurar backup
DELETE /settings/backup/delete # Deletar backup
```

### Configurações (Admin apenas)
```
GET    /settings               # Configurações gerais
PUT    /settings               # Atualiza configurações
GET    /settings/users         # Gerenciamento de usuários
POST   /settings/users         # Criar usuário
PUT    /settings/users/{id}    # Atualizar usuário
DELETE /settings/users/{id}    # Remover usuário
```

### Auditoria (Admin apenas)
```
GET    /audit                  # Logs de auditoria
GET    /audit/{id}             # Detalhes do log
```

### Perfil do Usuário
```
GET    /profile                # Página do perfil
PUT    /profile                # Atualiza perfil
PUT    /profile/password       # Altera senha
```

## ✅ Validações Robustas (v1.4.0)

### Form Request Classes

O sistema utiliza Form Request classes especializadas para validação:

- **StoreRevenueRequest** / **UpdateRevenueRequest**
- **StoreExpenseRequest** / **UpdateExpenseRequest**
- **StoreCategoryRequest** / **UpdateCategoryRequest**
- **StoreUserRequest** / **UpdateUserRequest**

### Validações de Categorias
```php
// StoreCategoryRequest
[
    'name' => 'required|string|max:255|unique:categories,name',
    'code' => 'required|string|max:50|unique:categories,code',
    'type' => 'required|in:fonte,bloco,grupo,acao',
    'parent_id' => 'nullable|exists:categories,id',
    'active' => 'boolean'
]

// UpdateCategoryRequest
[
    'name' => 'required|string|max:255|unique:categories,name,' . $this->category->id,
    'code' => 'required|string|max:50|unique:categories,code,' . $this->category->id,
    'type' => 'required|in:fonte,bloco,grupo,acao',
    'parent_id' => 'nullable|exists:categories,id',
    'active' => 'boolean'
]
```

### Validações de Receitas
```php
// StoreRevenueRequest / UpdateRevenueRequest
[
    'description' => 'required|string|max:255',
    'amount' => 'required|numeric|min:0.01|max:999999999.99',
    'date' => 'required|date|before_or_equal:today',
    'fonte_id' => 'required|exists:categories,id',
    'bloco_id' => 'required|exists:categories,id',
    'grupo_id' => 'required|exists:categories,id',
    'acao_id' => 'required|exists:categories,id',
    'observation' => 'nullable|string|max:1000'
]
```

### Validações de Despesas
```php
// StoreExpenseRequest / UpdateExpenseRequest
[
    'description' => 'required|string|max:255',
    'amount' => 'required|numeric|min:0.01|max:999999999.99',
    'date' => 'required|date|before_or_equal:today',
    'fonte_id' => 'required|exists:categories,id',
    'bloco_id' => 'required|exists:categories,id',
    'grupo_id' => 'required|exists:categories,id',
    'acao_id' => 'required|exists:categories,id',
    'classification_id' => 'required|exists:expense_classifications,id',
    'observation' => 'nullable|string|max:1000'
]
```

### Validações de Usuários
```php
// StoreUserRequest
[
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|string|min:8|confirmed',
    'role' => 'required|in:admin,operator'
]

// UpdateUserRequest
[
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email,' . $this->user->id,
    'password' => 'nullable|string|min:8|confirmed',
    'role' => 'required|in:admin,operator'
]
```

### Validações de Backup
```php
// BackupUploadRequest
[
    'backup_file' => 'required|file|mimes:sql,gz|max:102400', // 100MB max
    'confirm_restore' => 'required|accepted'
]
```

### Mensagens de Validação Personalizadas

Todas as mensagens estão em português brasileiro:

```php
'required' => 'O campo :attribute é obrigatório.',
'email' => 'O campo :attribute deve ser um endereço de email válido.',
'unique' => 'Este :attribute já está sendo usado.',
'min' => 'O campo :attribute deve ter pelo menos :min caracteres.',
'max' => 'O campo :attribute não pode ter mais que :max caracteres.',
'numeric' => 'O campo :attribute deve ser um número.',
'date' => 'O campo :attribute deve ser uma data válida.',
'before_or_equal' => 'O campo :attribute deve ser uma data anterior ou igual a :date.',
'exists' => 'O :attribute selecionado é inválido.',
'confirmed' => 'A confirmação do campo :attribute não confere.',
'accepted' => 'O campo :attribute deve ser aceito.'
```
    'bloco_id' => 'required|exists:categories,id',
    'grupo_id' => 'required|exists:categories,id',
    'acao_id' => 'required|exists:categories,id',
    'observation' => 'nullable|string'
]
```

## 🚀 Tecnologias Modernas (v1.4.0)

### Stack Tecnológico

- **Backend**: Laravel 11.38.2 (PHP 8.2+)
- **Frontend**: Blade Templates + Bootstrap 5 + Vite
- **Database**: MySQL 8.0+ / MariaDB 10.4+
- **Cache**: Redis (opcional) / File Cache
- **Build Tool**: Vite (substituiu Laravel Mix)
- **CSS Framework**: Bootstrap 5 + SASS
- **JavaScript**: ES6+ com módulos
- **Containerização**: Docker + Docker Compose
- **Web Server**: Nginx (produção) / Apache (alternativo)

### Vite Configuration

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
```

### JavaScript Moderno e AJAX

#### Seleção Dinâmica de Categorias
```javascript
// Carregamento dinâmico de subcategorias
class CategorySelector {
    constructor() {
        this.initEventListeners();
    }

    initEventListeners() {
        document.addEventListener('change', (e) => {
            if (e.target.matches('.category-select')) {
                this.loadChildren(e.target);
            }
        });
    }

    async loadChildren(selectElement) {
        const parentId = selectElement.value;
        const targetType = selectElement.dataset.target;
        
        if (!parentId || !targetType) return;

        try {
            const response = await fetch(`/categories/${parentId}/children`);
            const data = await response.json();
            this.updateSelectOptions(targetType, data);
        } catch (error) {
            console.error('Erro ao carregar subcategorias:', error);
        }
    }

    updateSelectOptions(targetType, options) {
        const targetSelect = document.querySelector(`[data-type="${targetType}"]`);
        if (!targetSelect) return;

        targetSelect.innerHTML = '<option value="">Selecione...</option>';
        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.id;
            optionElement.textContent = option.name;
            targetSelect.appendChild(optionElement);
        });
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new CategorySelector();
});
```

#### Formatação de Valores
```javascript
// Utilitários para formatação
class FormatUtils {
    static formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    }

    static formatDate(date) {
        return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
    }

    static formatNumber(value, decimals = 2) {
        return new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(value);
    }
}

// Máscara de valores monetários
class CurrencyMask {
    constructor(selector) {
        this.inputs = document.querySelectorAll(selector);
        this.init();
    }

    init() {
        this.inputs.forEach(input => {
            input.addEventListener('input', (e) => {
                this.applyMask(e.target);
            });
        });
    }

    applyMask(input) {
        let value = input.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2);
        input.value = FormatUtils.formatCurrency(value);
    }
}

// Inicializar máscaras
document.addEventListener('DOMContentLoaded', () => {
    new CurrencyMask('.currency-input');
});
```

### Responsividade e Mobile-First

#### SASS/CSS Moderno
```scss
// resources/sass/_variables.scss
$primary: #007bff;
$secondary: #6c757d;
$success: #28a745;
$danger: #dc3545;
$warning: #ffc107;
$info: #17a2b8;

// Breakpoints responsivos
$grid-breakpoints: (
  xs: 0,
  sm: 576px,
  md: 768px,
  lg: 992px,
  xl: 1200px,
  xxl: 1400px
);

// Sidebar responsivo
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 250px;
  background: $primary;
  transform: translateX(-100%);
  transition: transform 0.3s ease;
  z-index: 1050;

  &.show {
    transform: translateX(0);
  }

  @media (min-width: 768px) {
    position: relative;
    transform: translateX(0);
  }
}

// Overlay para mobile
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1040;
  display: none;

  &.show {
    display: block;
  }

  @media (min-width: 768px) {
    display: none !important;
  }
}

// Tabelas responsivas
.table-responsive-custom {
  @media (max-width: 767px) {
    .table {
      font-size: 0.875rem;
      
      th, td {
        padding: 0.5rem 0.25rem;
        
        &:nth-child(n+4) {
          display: none;
        }
      }
    }
  }
}
```

#### JavaScript para Responsividade
```javascript
// Gerenciamento do sidebar responsivo
class ResponsiveSidebar {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');
        this.overlay = document.querySelector('.sidebar-overlay');
        this.toggleBtn = document.querySelector('.sidebar-toggle');
        this.init();
    }

    init() {
        if (this.toggleBtn) {
            this.toggleBtn.addEventListener('click', () => {
                this.toggle();
            });
        }

        if (this.overlay) {
            this.overlay.addEventListener('click', () => {
                this.hide();
            });
        }

        // Fechar sidebar ao redimensionar para desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                this.hide();
            }
        });
    }

    toggle() {
        if (this.sidebar.classList.contains('show')) {
            this.hide();
        } else {
            this.show();
        }
    }

    show() {
        this.sidebar.classList.add('show');
        this.overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    hide() {
        this.sidebar.classList.remove('show');
        this.overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
}

// Inicializar sidebar responsivo
document.addEventListener('DOMContentLoaded', () => {
    new ResponsiveSidebar();
});
```

## Exportação de Relatórios

### Excel (usando Laravel Excel)
```php
class FinancialReport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;
    
    public function collection()
    {
        return collect($this->data);
    }
    
    public function headings(): array
    {
        return [
            'Data',
            'Descrição',
            'Valor',
            'Tipo'
        ];
    }
}
```

### PDF (usando DomPDF)
```php
use Barryvdh\DomPDF\Facade\PDF;

PDF::loadView('reports.pdf', $data)
    ->setPaper('a4', 'portrait')
    ->stream('relatorio.pdf');
```

## Logs de Auditoria

### Registro de Alterações
```php
trait Auditable
{
    public static function bootAuditable()
    {
        static::updated(function ($model) {
            self::logChanges('update', $model);
        });
        
        static::deleted(function ($model) {
            self::logChanges('delete', $model);
        });
    }
}
```

## Cache

### Configuração
```php
'stores' => [
    'file' => [
        'driver' => 'file',
        'path' => storage_path('framework/cache/data'),
    ],
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
    ],
],
```

### Uso
```php
// Exemplo de cache de categorias
Cache::remember('categories', 3600, function () {
    return Category::active()->get();
});
```

## Testes

### PHPUnit
```php
class CategoryTest extends TestCase
{
    public function test_can_create_category()
    {
        $data = [
            'name' => 'Test Category',
            'code' => 'TEST',
            'type' => 'fonte'
        ];
        
        $response = $this->post('/categories', $data);
        $response->assertStatus(201);
    }
}
```

## 🐳 Docker e Containerização (v1.4.0)

### Arquitetura Docker

O sistema possui configuração Docker completa com múltiplos serviços:

```yaml
# docker-compose.yml (resumido)
services:
  app:
    build: .
    volumes:
      - .:/var/www/html
    depends_on:
      - mysql
      - redis

  nginx:
    image: nginx:alpine
    ports:
      - "8080:80"
    volumes:
      - ./deployment/docker/nginx:/etc/nginx/conf.d

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: klgestorpub
      MYSQL_USER: klgestor
      MYSQL_PASSWORD: password

  redis:
    image: redis:alpine
    command: redis-server --appendonly yes

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8081:80"

  mailhog:
    image: mailhog/mailhog
    ports:
      - "8025:8025"
```

### Dockerfile Multi-Stage

```dockerfile
# Dockerfile (resumido)
FROM php:8.2-fpm as base

# Instalar extensões PHP
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Stage de desenvolvimento
FROM base as development
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Stage de produção
FROM base as production
COPY . /var/www/html
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build
```

### Scripts de Automação

#### Windows (docker-start.bat)
```batch
@echo off
echo Iniciando KL Gestor Pub com Docker...

if not exist ".env" (
    echo Copiando arquivo de ambiente...
    copy .env.docker .env
)

docker-compose up -d
echo.
echo ✅ Aplicação iniciada com sucesso!
echo 🌐 Acesse: http://localhost:8080
echo 📊 phpMyAdmin: http://localhost:8081
echo 📧 Mailhog: http://localhost:8025
pause
```

#### Linux/Mac (docker-start.sh)
```bash
#!/bin/bash
echo "🐳 Iniciando KL Gestor Pub com Docker..."

if [ ! -f ".env" ]; then
    echo "📋 Copiando arquivo de ambiente..."
    cp .env.docker .env
fi

docker-compose up -d

echo ""
echo "✅ Aplicação iniciada com sucesso!"
echo "🌐 Acesse: http://localhost:8080"
echo "📊 phpMyAdmin: http://localhost:8081"
echo "📧 Mailhog: http://localhost:8025"
```

## 💾 Sistema de Backup Avançado (v1.4.0)

### Comandos Artisan

```php
// app/Console/Commands/BackupDatabase.php
class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--compress} {--path=}';
    protected $description = 'Criar backup do banco de dados';

    public function handle()
    {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = $this->option('path') ?: storage_path('app/private/backups/');
        
        // Criar diretório se não existir
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $fullPath = $path . $filename;
        
        // Comando mysqldump
        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s',
            config('database.connections.mysql.host'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $fullPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            // Comprimir se solicitado
            if ($this->option('compress')) {
                $compressedPath = $fullPath . '.gz';
                exec("gzip {$fullPath}", $output, $returnCode);
                $fullPath = $compressedPath;
            }

            $this->info("✅ Backup criado: {$fullPath}");
            
            // Log de auditoria
            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'backup_created',
                'model_type' => 'Database',
                'model_id' => null,
                'new_values' => ['file' => basename($fullPath)]
            ]);
            
            return $fullPath;
        } else {
            $this->error('❌ Erro ao criar backup');
            return false;
        }
    }
}
```

### Controller de Backup

```php
// app/Http/Controllers/BackupController.php
class BackupController extends Controller
{
    public function create()
    {
        try {
            $backupPath = Artisan::call('backup:database', ['--compress' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup criado com sucesso!',
                'file' => basename($backupPath)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,gz|max:102400'
        ]);

        try {
            // Criar backup antes de restaurar
            $preRestoreBackup = Artisan::call('backup:database', ['--compress' => true]);
            
            $file = $request->file('backup_file');
            $tempPath = storage_path('app/temp/' . $file->getClientOriginalName());
            $file->move(dirname($tempPath), basename($tempPath));

            // Descomprimir se necessário
            if (pathinfo($tempPath, PATHINFO_EXTENSION) === 'gz') {
                exec("gunzip {$tempPath}");
                $tempPath = str_replace('.gz', '', $tempPath);
            }

            // Restaurar banco
            $command = sprintf(
                'mysql -h%s -u%s -p%s %s < %s',
                config('database.connections.mysql.host'),
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.database'),
                $tempPath
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                // Limpar arquivo temporário
                unlink($tempPath);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Backup restaurado com sucesso!'
                ]);
            } else {
                throw new Exception('Falha na restauração do banco de dados');
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao restaurar backup: ' . $e->getMessage()
            ], 500);
        }
    }
}
```

## 🚀 Deploy e Produção (v1.4.0)

### Requisitos do Servidor
- **PHP**: >= 8.2 (recomendado 8.3+)
- **MySQL/MariaDB**: >= 8.0 / >= 10.4
- **Nginx**: >= 1.18 (recomendado)
- **Composer**: >= 2.0
- **Node.js**: >= 18.x (para build de assets)
- **Redis**: >= 6.0 (opcional, para cache)

### Deploy Tradicional

```bash
# 1. Clonar repositório
git clone https://github.com/rayhenrique/klgestorpub.git
cd klgestorpub

# 2. Instalar dependências
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# 3. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 4. Configurar banco de dados
php artisan migrate --force
php artisan db:seed --force

# 5. Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Configurar permissões
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

### Deploy com Docker

```bash
# 1. Clonar repositório
git clone https://github.com/rayhenrique/klgestorpub.git
cd klgestorpub

# 2. Configurar ambiente de produção
cp .env.docker .env
# Editar .env com configurações de produção

# 3. Build e iniciar containers
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# 4. Executar migrações
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force

# 5. Otimizar aplicação
docker-compose exec app php artisan optimize
```

### Processo de Atualização

```bash
# 1. Modo de manutenção
php artisan down --message="Atualizando sistema..."

# 2. Backup antes da atualização
php artisan backup:database --compress

# 3. Atualizar código
git pull origin main

# 4. Atualizar dependências
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# 5. Executar migrações
php artisan migrate --force

# 6. Limpar e recriar caches
php artisan optimize:clear
php artisan optimize

# 7. Sair do modo de manutenção
php artisan up
```

## Manutenção

### Backup
- Backup diário do banco de dados
- Backup semanal dos arquivos
- Rotação de logs a cada 7 dias

### Monitoramento
- Log de erros do Laravel
- Log de acesso do servidor web
- Monitoramento de performance
- Alertas de erro por email

## Segurança

### Práticas Implementadas
- Validação de entrada
- Sanitização de saída
- Proteção CSRF
- Rate limiting
- Autenticação forte
- Logs de auditoria
- Backup regular
- HTTPS forçado

### Atualizações
- Manter Laravel atualizado
- Verificar vulnerabilidades com `composer audit`
- Atualizar dependências regularmente
- Monitorar boletins de segurança
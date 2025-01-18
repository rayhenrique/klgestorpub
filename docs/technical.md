# Documentação Técnica - KLGestorPub

## Arquitetura do Sistema

### Padrão MVC
O sistema segue o padrão MVC (Model-View-Controller) do Laravel, com a seguinte organização:

- **Models**: Representam as entidades do sistema e suas relações
- **Views**: Templates Blade para renderização das interfaces
- **Controllers**: Lógica de negócio e manipulação de dados

### Estrutura de Diretórios

```
app/
├── Console/
├── Exports/           # Classes para exportação Excel/PDF
├── Http/
│   ├── Controllers/   # Controllers da aplicação
│   └── Middleware/    # Middlewares de autenticação/autorização
├── Models/            # Models do Eloquent
└── Traits/           # Traits compartilhados
    
resources/
├── views/            # Templates Blade
│   ├── layouts/      # Layouts base
│   ├── auth/         # Views de autenticação
│   ├── categories/   # Views de categorias
│   ├── revenues/     # Views de receitas
│   ├── expenses/     # Views de despesas
│   └── reports/      # Views de relatórios
    
database/
├── migrations/       # Migrações do banco
└── seeders/         # Seeders para dados iniciais

public/
├── css/             # Arquivos CSS compilados
├── js/              # Scripts JavaScript
└── assets/          # Imagens e outros assets
```

## Banco de Dados

### Diagrama ER
```
User
├── id
├── name
├── email
├── password
├── role (admin/operator)
└── active

Category
├── id
├── name
├── code
├── type (fonte/bloco/grupo/acao)
├── parent_id
└── active

Revenue
├── id
├── description
├── amount
├── date
├── fonte_id
├── bloco_id
├── grupo_id
├── acao_id
└── observation

Expense
├── id
├── description
├── amount
├── date
├── fonte_id
├── bloco_id
├── grupo_id
├── acao_id
├── classification_id
└── observation

ExpenseClassification
├── id
├── name
└── active

CitySetting
├── id
├── city_name
├── city_hall_name
├── address
├── ibge_code
├── state
├── zip_code
├── phone
├── email
└── mayor_name

AuditLog
├── id
├── user_id
├── action
├── model_type
├── model_id
├── old_values
├── new_values
└── created_at
```

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

## Autenticação e Autorização

### Middleware

- **auth**: Verifica se o usuário está autenticado
- **admin**: Verifica se o usuário é administrador
- **operator**: Verifica se o usuário é operador

### Roles

- **Admin**: Acesso total ao sistema
- **Operator**: Acesso limitado (sem criar/editar categorias)

## APIs e Endpoints

### Categorias
```
GET    /categories              # Lista categorias
POST   /categories             # Cria categoria
PUT    /categories/{id}        # Atualiza categoria
DELETE /categories/{id}        # Remove categoria
GET    /categories/{id}/children # Obtém subcategorias
```

### Receitas
```
GET    /revenues               # Lista receitas
POST   /revenues              # Cria receita
PUT    /revenues/{id}         # Atualiza receita
DELETE /revenues/{id}         # Remove receita
```

### Despesas
```
GET    /expenses              # Lista despesas
POST   /expenses             # Cria despesa
PUT    /expenses/{id}        # Atualiza despesa
DELETE /expenses/{id}        # Remove despesa
```

### Relatórios
```
GET    /reports              # Lista relatórios
POST   /reports/generate    # Gera relatório
```

## Validações

### Categorias
```php
[
    'name' => 'required|string|max:255',
    'code' => 'required|string|max:50',
    'type' => 'required|in:fonte,bloco,grupo,acao',
    'parent_id' => 'nullable|exists:categories,id',
    'active' => 'boolean'
]
```

### Receitas/Despesas
```php
[
    'description' => 'required|string|max:255',
    'amount' => 'required|numeric|min:0',
    'date' => 'required|date',
    'fonte_id' => 'required|exists:categories,id',
    'bloco_id' => 'required|exists:categories,id',
    'grupo_id' => 'required|exists:categories,id',
    'acao_id' => 'required|exists:categories,id',
    'observation' => 'nullable|string'
]
```

## JavaScript e AJAX

### Seleção Dinâmica de Categorias
```javascript
// Exemplo de chamada AJAX para carregar subcategorias
function loadChildren(parentId, type) {
    $.get(`/categories/${parentId}/children`, function(data) {
        updateSelectOptions(type, data);
    });
}
```

### Formatação de Valores
```javascript
// Exemplo de formatação de valores monetários
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}
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

## Deploy

### Requisitos do Servidor
- PHP >= 8.2
- MySQL >= 5.7
- Composer
- Node.js (para compilação de assets)

### Processo de Deploy
1. Clone o repositório
2. Instale dependências
3. Configure ambiente
4. Execute migrações
5. Compile assets
6. Configure servidor web

### Comandos
```bash
# Instalação
composer install --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Atualização
php artisan down
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
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
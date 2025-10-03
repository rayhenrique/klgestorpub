# KL Gestor Pub v1.4.0

Sistema de Gestão de Contas Públicas desenvolvido para auxiliar na administração e controle financeiro de órgãos públicos municipais.

## 🎯 Sobre o Sistema

O **KL Gestor Pub** é uma solução completa e moderna para gestão de contas públicas, oferecendo:

### 💰 **Gestão Financeira**
- ✅ Controle detalhado de receitas e despesas
- ✅ Sistema hierárquico de categorização (Fonte → Bloco → Grupo → Ação)
- ✅ Classificação específica de despesas
- ✅ Balanço automático e análise de variações

### 📊 **Relatórios e Analytics**
- ✅ Relatórios financeiros avançados com filtros
- ✅ Exportação para PDF e Excel
- ✅ Dashboard com métricas em tempo real
- ✅ Gráficos interativos e comparativos

### 🔐 **Segurança e Auditoria**
- ✅ Sistema completo de auditoria de operações
- ✅ Controle de usuários com roles (Admin/Operador)
- ✅ Logs detalhados de todas as ações
- ✅ Validação robusta de dados

### ⚙️ **Configuração e Personalização**
- ✅ Configurações personalizadas por município
- ✅ Interface responsiva em português brasileiro
- ✅ Timezone configurável (padrão: America/Maceio)
- ✅ Temas e layouts adaptáveis

## Funcionalidades

## 🚀 Melhorias Recentes (v1.4.0)

### 📱 **Sistema 100% Responsivo**
- **Design Mobile-First**: Interface totalmente adaptada para smartphones e tablets
- **Sidebar Responsivo**: Menu lateral colapsável com animações suaves
- **Breakpoints Inteligentes**: Otimizado para mobile (<768px), tablet (768px-1024px) e desktop (>1024px)
- **Tabelas Adaptáveis**: Scroll horizontal e colunas ocultas em telas menores
- **Formulários Otimizados**: Layout responsivo em todos os CRUDs
- **Navegação Mobile**: Botão hamburger e overlay para melhor UX

### 💾 **Sistema de Backup e Restauração Completo**
- **Backup Automático**: Criação de backups compactados (.gz) via interface web
- **Download Seguro**: Sistema de download com autenticação e validação
- **Restauração Inteligente**: Upload e restauração de backups com pré-validação
- **Backup Pré-Restauração**: Criação automática de backup antes de restaurar
- **Comandos Artisan**: `backup:database` e `backup:restore` para automação
- **Logs de Auditoria**: Registro completo de todas as operações de backup
- **Validação de Arquivos**: Suporte a .sql e .gz com verificação de integridade

### ♿ **Acessibilidade WAI-ARIA**
- **Conformidade WCAG**: Implementação de diretrizes de acessibilidade
- **Navegação por Teclado**: Suporte completo para navegação sem mouse
- **Screen Readers**: Compatibilidade com leitores de tela
- **Atributos ARIA**: Implementação correta de aria-labels e roles
- **Contraste Otimizado**: Cores e contrastes adequados para baixa visão

### 🏗️ **Arquitetura Completamente Reestruturada**
- **Migração Limpa**: Removida arquitetura obsoleta da tabela `transactions`
- **Tabelas Especializadas**: Separação clara entre `revenues` e `expenses`
- **Performance Otimizada**: Consultas mais eficientes e relacionamentos otimizados
- **Estabilidade Total**: Zero conflitos de foreign key constraints

### 🔧 **Correções Críticas e Melhorias**
- **Migrações Estáveis**: Sistema de migrações completamente funcional
- **Código Limpo**: Remoção de 262 linhas de código obsoleto
- **Validação Aprimorada**: Tratamento robusto de erros e exceções
- **Interface Polida**: Melhorias visuais e de usabilidade
- **GitHub Atualizado**: Repositório sincronizado com as últimas correções

### 🔧 **Validação Aprimorada**
- **Form Request Classes**: `StoreRevenueRequest`, `UpdateRevenueRequest`, `StoreExpenseRequest`, `UpdateExpenseRequest`
- **Validação Robusta**: Regras de validação em português com mensagens personalizadas
- **Segurança**: Validação de valores monetários, datas e relacionamentos de categorias

### 🧪 **Sistema de Testes**
- **Cobertura Completa**: Testes para gerenciamento de receitas, despesas e relatórios
- **Factories**: `RevenueFactory`, `ExpenseFactory`, `CategoryFactory` para dados de teste
- **Testes Funcionais**: Validação de CRUD, autorização e relacionamentos
- **PHPUnit**: Framework de testes integrado com Laravel

### 🏗️ **Arquitetura Melhorada**
- **Service Layer**: `ReportService` para centralizar lógica de relatórios
- **Separação de Responsabilidades**: Controllers mais limpos e focados
- **Manutenibilidade**: Código mais organizado e reutilizável

### 📊 **Relatórios Otimizados**
- Relatórios detalhados de:
  - Receitas
  - Despesas
  - Balanço
  - Classificação de Despesas
- Exportação para PDF e Excel
- Visualização por período (Diário, Mensal, Anual)
- Filtros por Fonte, Bloco, Grupo e Ação
- Formatação de datas no padrão brasileiro
- Valores monetários formatados em Real (R$)

### Categorização
- Sistema hierárquico de categorias:
  - Fontes
  - Blocos
  - Grupos
  - Ações
- Classificação detalhada de despesas

### Segurança
- Autenticação de usuários
- Controle de permissões
- Logs de auditoria
- Backup automático

### Personalização
- Configurações por município
- Interface responsiva
- Temas personalizáveis

## 📁 Estrutura do Projeto

O projeto segue uma estrutura organizada e limpa, seguindo as melhores práticas do Laravel:

```
klgestorpub/
├── app/                    # Código da aplicação Laravel
│   ├── Console/           # Comandos Artisan customizados
│   ├── Http/              # Controllers, Middleware, Requests
│   ├── Models/            # Models Eloquent
│   ├── Services/          # Camada de serviços
│   └── Traits/            # Traits reutilizáveis
├── config/                # Configurações da aplicação
├── database/              # Migrações, seeders e factories
├── deployment/            # Scripts e configurações de deploy
│   ├── docker/           # Configurações Docker (movido)
│   └── *.sh              # Scripts de instalação
├── docs/                  # Documentação do projeto
├── infrastructure/        # Arquivos de infraestrutura
│   ├── logs/             # Logs externos (nginx, etc.)
│   ├── secrets/          # Arquivos sensíveis
│   └── volumes/          # Volumes Docker
├── lang/                  # Arquivos de tradução
├── public/                # Assets públicos
├── resources/             # Views, CSS, JS, SASS
├── routes/                # Definições de rotas
├── storage/               # Arquivos de cache e logs Laravel
└── tests/                 # Testes automatizados
```

### **Organização Melhorada:**
- ✅ **Arquivos SSH removidos** da raiz (segurança)
- ✅ **Diretórios vazios removidos** (municipalities, scripts, ssl-certificates)
- ✅ **Infrastructure centralizada** em `/infrastructure/`
- ✅ **Docker movido** para `/deployment/docker/`
- ✅ **Assets otimizados** (apenas versões mais recentes)
- ✅ **Gitignore atualizado** para nova estrutura

## 📋 Requisitos do Sistema

### **Requisitos Mínimos:**
- **PHP**: >= 8.2 (recomendado 8.3+)
- **Composer**: >= 2.0
- **MySQL/MariaDB**: >= 8.0 / >= 10.4
- **Node.js**: >= 18.x
- **NPM**: >= 9.x

### **Extensões PHP Necessárias:**
- `php-mysql`, `php-mbstring`, `php-xml`, `php-curl`
- `php-zip`, `php-gd`, `php-bcmath`, `php-intl`

### **Recursos do Servidor:**
- **RAM**: Mínimo 512MB (recomendado 1GB+)
- **Disco**: Mínimo 1GB livre
- **Processador**: Qualquer arquitetura x64

## 🛠️ Instalação

### 🐳 **Instalação com Docker (Recomendado)**

A forma mais rápida e confiável de executar o KL Gestor Pub é usando Docker:

#### **Windows:**
```powershell
# PowerShell
# 1. Clonar o repositório
git clone https://github.com/rayhenrique/klgestorpub.git
cd klgestorpub

# 2. Configurar ambiente Docker
.\docker-setup.bat

# 3. Iniciar aplicação
.\docker-start.bat

# 4. Acessar: http://localhost:8080
```

```cmd
# Command Prompt
# 1. Clonar o repositório
git clone https://github.com/rayhenrique/klgestorpub.git
cd klgestorpub

# 2. Configurar ambiente Docker
docker-setup.bat

# 3. Iniciar aplicação
docker-start.bat

# 4. Acessar: http://localhost:8080
```

#### **Linux/Mac:**
```bash
# 1. Clonar o repositório
git clone https://github.com/rayhenrique/klgestorpub.git
cd klgestorpub

# 2. Dar permissões e configurar
chmod +x docker-*.sh
./docker-setup.sh

# 3. Iniciar aplicação
./docker-start.sh

# 4. Acessar: http://localhost:8080
```

#### **Serviços Disponíveis:**
- **Aplicação:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081
- **Mailhog:** http://localhost:8025
- **Health Check:** http://localhost:8080/health

### 💻 **Instalação Local (Tradicional)**

#### **Pré-requisitos:**
- PHP 8.2+, MySQL 8.0+, Node.js 18+, Composer 2.0+

```bash
# 1. Clonar o repositório
git clone https://github.com/rayhenrique/klgestorpub.git
cd klgestorpub

# 2. Instalar dependências PHP
composer install

# 3. Instalar dependências Node.js
npm install

# 4. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 5. Configurar banco de dados no .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=klgestorpub
# DB_USERNAME=root
# DB_PASSWORD=sua_senha

# 6. Executar migrações e seeders
php artisan migrate:fresh --seed

# 7. Criar link simbólico para storage
php artisan storage:link

# 8. Compilar assets
npm run build

# 9. Iniciar servidor de desenvolvimento
php artisan serve
```

### **⚙️ Configurações Importantes no .env:**
```env
APP_NAME="KL Gestor Pub"
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_TIMEZONE=America/Maceio
```

## Instalação no Ubuntu Server (VPS)

### 1. Preparação do Servidor
```bash
# Atualizar o sistema
sudo apt update
sudo apt upgrade -y

# Instalar dependências básicas
sudo apt install -y curl git unzip

# Instalar Nginx
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx

# Instalar MySQL
sudo apt install -y mysql-server
sudo systemctl enable mysql
sudo systemctl start mysql

# Configurar segurança do MySQL
sudo mysql_secure_installation

# Instalar PHP e extensões necessárias
sudo apt install -y php8.1-fpm php8.1-cli php8.1-mysql php8.1-zip php8.1-gd \
    php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath php8.1-intl

# Instalar Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Instalar Node.js e NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Configuração do Banco de Dados
```bash
# Acessar o MySQL
sudo mysql

# Criar banco e usuário (substitua 'senha_segura' por uma senha forte)
CREATE DATABASE klgestorpub;
CREATE USER 'klgestor'@'localhost' IDENTIFIED BY 'senha_segura';
GRANT ALL PRIVILEGES ON klgestorpub.* TO 'klgestor'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Instalação da Aplicação
```bash
# Navegar para o diretório web
cd /var/www

# Clonar o repositório
sudo git clone https://github.com/rayhenrique/klgestorpub.git

# Configurar permissões
sudo chown -R www-data:www-data klgestorpub
sudo chmod -R 755 klgestorpub
cd klgestorpub

# Instalar dependências
composer install --no-dev
npm install
npm run build

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Editar o arquivo .env com as configurações do banco
sudo nano .env

# Executar migrações e seeds
php artisan migrate --seed
php artisan storage:link
```

### 4. Configuração do Nginx
```bash
# Criar arquivo de configuração do site
sudo nano /etc/nginx/sites-available/klgestorpub

# Adicionar a configuração (substitua example.com pelo seu domínio):
server {
    listen 80;
    server_name example.com;
    root /var/www/klgestorpub/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Ativar o site
sudo ln -s /etc/nginx/sites-available/klgestorpub /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 5. Configuração do SSL (Opcional mas Recomendado)
```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obter certificado SSL
sudo certbot --nginx -d example.com

# O Certbot irá modificar automaticamente a configuração do Nginx
```

### 6. Configuração do Supervisor (Para Filas)
```bash
# Instalar Supervisor
sudo apt install -y supervisor

# Criar arquivo de configuração
sudo nano /etc/supervisor/conf.d/klgestorpub-worker.conf

# Adicionar configuração:
[program:klgestorpub-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/klgestorpub/artisan queue:work
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/klgestorpub/storage/logs/worker.log
stopwaitsecs=3600

# Recarregar Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

## Suporte

Para suporte técnico e comercial, entre em contato através do email: rayhenrique@gmail.com

## Licença

Este software é proprietário e está protegido por direitos autorais. O uso, cópia, modificação ou distribuição deste software sem a devida autorização é estritamente proibido.

## Changelog

### Versão 1.0.0 (08/01/2024)
- Lançamento inicial do sistema
- Implementação do controle de despesas e receitas
- Sistema de categorias hierárquicas
- Relatórios financeiros básicos
- Sistema de autenticação e autorização
- Configurações por município

### Versão 1.1.0
- Adição de logs de auditoria
- Melhorias na interface do usuário
- Implementação de relatórios avançados
- Sistema de backup automático
- Documentação completa do sistema

### Versão 1.4.0 (Atual - Janeiro 2025)
- **🏗️ Arquitetura Reestruturada**: Migração completa para tabelas especializadas (revenues/expenses)
- **🔧 Correções Críticas**: Eliminação de conflitos de migração e foreign key constraints
- **🧹 Código Limpo**: Remoção de 262 linhas de código obsoleto e 5 arquivos desnecessários
- **⚡ Performance**: Otimização de consultas e relacionamentos de banco de dados
- **🔒 Estabilidade**: Sistema totalmente funcional sem erros de migração
- **📚 Documentação**: Atualização completa da documentação técnica e manual
- **🚀 Deploy**: Preparado para atualizações futuras sem conflitos
- **✅ Status**: Sistema 100% operacional e testado

### Versão 1.3.0 (Agosto 2025)
- **Melhorias Principais**: Validação aprimorada, testes abrangentes, arquitetura melhorada
- **Validação de Formulários**: Implementação de Request classes dedicadas para validação
- **Testes**: Suite completa de testes com PHPUnit e factories para modelos
- **Arquitetura**: Separação da lógica de negócios com Service classes
- **Qualidade do Código**: Melhor organização, manutenibilidade e práticas de produção
- **Tecnologia**: Laravel 11.31, PHP 8.2+, Bootstrap 5, Vite

## 🆘 Troubleshooting

### **Problemas Comuns e Soluções:**

#### **Erro de Migração:**
```bash
# Se houver erro de foreign key constraint:
php artisan migrate:fresh --seed
```

#### **Erro de Permissões:**
```bash
# Linux/Mac:
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Windows (executar como administrador):
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

#### **Assets não carregando:**
```bash
npm run build
php artisan config:clear
php artisan cache:clear
```

#### **Banco de dados não conecta:**
1. Verifique as credenciais no `.env`
2. Certifique-se que o MySQL está rodando
3. Teste a conexão: `php artisan tinker` → `DB::connection()->getPdo()`

---

## 🧭 Fluxo de Branches

![CI Status](https://github.com/rayhenrique/klgestorpub/actions/workflows/ci.yml/badge.svg?branch=develop)
![Deploy Status](https://github.com/rayhenrique/klgestorpub/actions/workflows/deploy.yml/badge.svg?branch=main)

- `main`: branch estável, sempre pronta para produção
- `develop`: integração contínua de features
- `feature/*`: desenvolvimento de funcionalidades (ex.: `feature/reports`, `feature/cache`, `feature/pdf`)
- `release/x.y.z` (opcional): preparação de releases
- `hotfix/*` (opcional): correções emergenciais na produção

## ⚙️ CI/CD com GitHub Actions

- CI (`.github/workflows/ci.yml`):
  - Executa em `push` para `develop` e `feature/*` e em PR para `main`/`develop`
  - Passos: checkout, setup PHP 8.2, `composer install`, preparar `.env`, unit tests (`phpunit --testsuite Unit`), code style (`pint --test`), `composer validate`
- Deploy (`.github/workflows/deploy.yml`):
  - Executa em `push` para `main`
  - Faz deploy via SSH para o VPS e roda `composer install`, `php artisan migrate`, e cache de rotas/config/views

### Secrets necessários para Deploy
- `SSH_PRIVATE_KEY`: chave privada com acesso ao servidor
- `SSH_HOST`: host do VPS
- `SSH_USER`: usuário remoto

## 🤝 Guia de Contribuição

1. Crie sua branch a partir de `develop`:
   - `git checkout develop && git pull`
   - `git checkout -b feature/<nome-da-feature>`
2. Siga commits claros (ex.: Conventional Commits: `feat: nova tela de relatórios`) 
3. Garanta que o CI passe (tests + pint) antes de abrir PR
4. Abra PR para `develop` com descrição objetiva e checklist
5. Após merge em `develop`, promova para `main` via release/deploy

## 📦 Como rodar o CI localmente

- `composer install`
- `cp .env.example .env && php artisan key:generate`
- `vendor/bin/phpunit --testsuite Unit`
- `vendor/bin/pint --test`

**Todos os direitos reservados © 2025 KL Gestor Pub v1.4.0**  
**Desenvolvido por Ray Henrique** | **Email**: rayhenrique@gmail.com

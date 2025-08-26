# KL Gestor Pub

Sistema de Gestão de Contas Públicas desenvolvido para auxiliar na administração e controle financeiro de órgãos públicos.

## Sobre o Sistema

O KL Gestor Pub é uma solução completa para gestão de contas públicas, oferecendo:
- Controle de despesas e receitas
- Classificação de despesas
- Relatórios financeiros
- Auditoria de operações
- Gestão de usuários e permissões
- Configurações personalizadas por município

## Funcionalidades

## Melhorias Recentes (v1.3.0)

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

## Requisitos

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js e NPM

## Instalação Local

1. Clone o repositório
2. Execute `composer install`
3. Execute `npm install`
4. Configure o arquivo `.env`
5. Execute `php artisan migrate`
6. Execute `php artisan db:seed`
7. Execute `npm run build`

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

### Versão 1.3.0 (Atual - 26/08/2025)
- **Melhorias Principais**: Validação aprimorada, testes abrangentes, arquitetura melhorada
- **Validação de Formulários**: Implementação de Request classes dedicadas para validação
- **Testes**: Suite completa de testes com PHPUnit e factories para modelos
- **Arquitetura**: Separação da lógica de negócios com Service classes
- **Qualidade do Código**: Melhor organização, manutenibilidade e práticas de produção
- **Tecnologia**: Laravel 11.31, PHP 8.2+, Bootstrap 5, Vite
- **Status**: Todos os testes passando ✅

Todos os direitos reservados 2025 KL Gestor Pub v1.3.0

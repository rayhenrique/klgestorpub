# Guia de Criação de Nova Instância - SISGERP

---

## Visão Geral

Este documento fornece instruções detalhadas para criar uma nova instância do sistema SISGERP (Sistema de Gestão de Contas Públicas), garantindo isolamento completo entre instâncias e configuração adequada do ambiente.

---

## 1. Pré-requisitos de Instalação

### 1.1 Requisitos do Servidor
- **Sistema Operacional**: Ubuntu 20.04+ ou CentOS 8+
- **PHP**: 8.2 ou superior
- **MySQL**: 8.0 ou superior
- **Nginx**: 1.18+ ou Apache 2.4+
- **Composer**: 2.0+
- **Node.js**: 18+ (para compilação de assets)
- **SSL**: Certificado válido configurado

### 1.2 Extensões PHP Necessárias
```bash
php-fpm
php-mysql
php-mbstring
php-xml
php-curl
php-zip
php-gd
php-intl
php-bcmath
php-soap
```

### 1.3 Permissões do Sistema
- Usuário com acesso SSH ao servidor
- Permissões sudo para configuração do Nginx/Apache
- Acesso ao MySQL para criação de bancos e usuários

---

## 2. Estrutura de Diretórios

### 2.1 Convenção de Nomenclatura
```
/var/www/klgestorpub-[nome-instancia]/
├── app/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
└── .env
```

**Exemplo para cidade "São Paulo":**
```
/var/www/klgestorpub-saopaulo/
```

### 2.2 Estrutura de Banco de Dados
```
Database: klgestorpub_[nome_instancia]
User: klgestor_[nome_instancia]
Password: [senha_segura_gerada]
```

---

## 3. Passo a Passo de Configuração

### 3.1 Preparação do Ambiente

#### 3.1.1 Conectar ao Servidor
```bash
ssh root@[IP_SERVIDOR]
```

#### 3.1.2 Navegar para o Diretório Web
```bash
cd /var/www/
```

### 3.2 Criação da Nova Instância

#### 3.2.1 Copiar Aplicação Base
```bash
# Copiar da instância original
cp -r klgestorpub klgestorpub-[nome-instancia]

# Exemplo:
cp -r klgestorpub klgestorpub-saopaulo
```

#### 3.2.2 Configurar Permissões
```bash
cd klgestorpub-[nome-instancia]
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

### 3.3 Configuração do Banco de Dados

#### 3.3.1 Criar Banco e Usuário
```sql
-- Conectar ao MySQL
mysql -u root -p

-- Criar banco de dados
CREATE DATABASE klgestorpub_[nome_instancia] CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário dedicado
CREATE USER 'klgestor_[nome_instancia]'@'localhost' IDENTIFIED BY '[senha_segura]';

-- Conceder permissões
GRANT ALL PRIVILEGES ON klgestorpub_[nome_instancia].* TO 'klgestor_[nome_instancia]'@'localhost';

-- Aplicar mudanças
FLUSH PRIVILEGES;

-- Sair do MySQL
EXIT;
```

#### 3.3.2 Exemplo Prático
```sql
-- Para instância "saopaulo"
CREATE DATABASE klgestorpub_saopaulo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'klgestor_saopaulo'@'localhost' IDENTIFIED BY 'Sp2024@Secure!';
GRANT ALL PRIVILEGES ON klgestorpub_saopaulo.* TO 'klgestor_saopaulo'@'localhost';
FLUSH PRIVILEGES;
```

### 3.4 Configuração do Arquivo .env

#### 3.4.1 Copiar e Editar .env
```bash
cd /var/www/klgestorpub-[nome-instancia]
cp .env.example .env
nano .env
```

#### 3.4.2 Configurações Obrigatórias
```env
# Informações da Aplicação
APP_NAME="SISGERP - [Nome da Cidade]"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://[dominio]/[nome-instancia]

# Configurações de Localização
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR
APP_TIMEZONE=America/Sao_Paulo

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=klgestorpub_[nome_instancia]
DB_USERNAME=klgestor_[nome_instancia]
DB_PASSWORD=[senha_do_banco]

# Sessão e Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database

# Logs
LOG_CHANNEL=daily
LOG_LEVEL=info

# Email (configurar conforme necessário)
MAIL_MAILER=smtp
MAIL_HOST=[servidor_smtp]
MAIL_PORT=587
MAIL_USERNAME=[usuario_email]
MAIL_PASSWORD=[senha_email]
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@[dominio]"
MAIL_FROM_NAME="SISGERP - [Nome da Cidade]"
```

#### 3.4.3 Exemplo Completo (.env)
```env
APP_NAME="SISGERP - São Paulo"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:generated_key_here
APP_URL=https://sisgerp.com/saopaulo
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR
APP_TIMEZONE=America/Sao_Paulo

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=klgestorpub_saopaulo
DB_USERNAME=klgestor_saopaulo
DB_PASSWORD=Sp2024@Secure!

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
LOG_CHANNEL=daily
LOG_LEVEL=info

MAIL_MAILER=smtp
MAIL_FROM_ADDRESS="noreply@sisgerp.com"
MAIL_FROM_NAME="SISGERP - São Paulo"
```

### 3.5 Configuração do Laravel

#### 3.5.1 Gerar Chave da Aplicação
```bash
cd /var/www/klgestorpub-[nome-instancia]
php artisan key:generate
```

#### 3.5.2 Executar Migrações
```bash
php artisan migrate --force
```

#### 3.5.3 Popular Dados Iniciais
```bash
php artisan db:seed --force
```

#### 3.5.4 Limpar e Otimizar Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### 3.6 Configuração do Nginx

#### 3.6.1 Editar Configuração Principal
```bash
nano /etc/nginx/sites-available/klgestorpub
```

#### 3.6.2 Adicionar Location Block
```nginx
# Adicionar dentro do server block existente
location /[nome-instancia] {
    alias /var/www/klgestorpub-[nome-instancia]/public;
    try_files $uri $uri/ @[nome-instancia];
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_param SCRIPT_FILENAME $request_filename;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
}

location @[nome-instancia] {
    rewrite /[nome-instancia]/(.*)$ /[nome-instancia]/index.php?/$1 last;
}

# Assets estáticos
location /[nome-instancia]/css/ {
    alias /var/www/klgestorpub-[nome-instancia]/public/css/;
    expires 1y;
    add_header Cache-Control "public, immutable";
}

location /[nome-instancia]/js/ {
    alias /var/www/klgestorpub-[nome-instancia]/public/js/;
    expires 1y;
    add_header Cache-Control "public, immutable";
}

location /[nome-instancia]/images/ {
    alias /var/www/klgestorpub-[nome-instancia]/public/images/;
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

#### 3.6.3 Exemplo Prático (São Paulo)
```nginx
location /saopaulo {
    alias /var/www/klgestorpub-saopaulo/public;
    try_files $uri $uri/ @saopaulo;
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_param SCRIPT_FILENAME $request_filename;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
}

location @saopaulo {
    rewrite /saopaulo/(.*)$ /saopaulo/index.php?/$1 last;
}
```

#### 3.6.4 Testar e Recarregar Nginx
```bash
nginx -t
systemctl reload nginx
```

### 3.7 Configuração dos Controllers de Autenticação

#### 3.7.1 Editar LoginController
```bash
nano /var/www/klgestorpub-[nome-instancia]/app/Http/Controllers/Auth/LoginController.php
```

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function redirectPath()
    {
        return '/dashboard';
    }
}
```

#### 3.7.2 Aplicar em Outros Controllers
Repetir a mesma configuração em:
- `VerificationController.php`
- `ResetPasswordController.php`
- `RegisterController.php` (se existir)

---

## 4. Verificação de Ambiente

### 4.1 Testes de Conectividade

#### 4.1.1 Verificar Acesso à Aplicação
```bash
curl -I https://[dominio]/[nome-instancia]/
# Deve retornar HTTP 200 OK
```

#### 4.1.2 Verificar Página de Login
```bash
curl -I https://[dominio]/[nome-instancia]/login
# Deve retornar HTTP 200 OK
```

#### 4.1.3 Verificar Assets
```bash
curl -I https://[dominio]/[nome-instancia]/css/app.css
curl -I https://[dominio]/[nome-instancia]/js/app.js
# Devem retornar HTTP 200 OK
```

### 4.2 Testes de Banco de Dados

#### 4.2.1 Verificar Conexão
```bash
cd /var/www/klgestorpub-[nome-instancia]
php artisan tinker
```

```php
// No tinker
DB::connection()->getPdo();
// Deve retornar objeto PDO sem erros

// Verificar tabelas
DB::select('SHOW TABLES');
// Deve listar todas as tabelas migradas
```

### 4.3 Testes Funcionais

#### 4.3.1 Checklist de Verificação
- [ ] Página inicial carrega corretamente
- [ ] Página de login acessível
- [ ] CSS e JavaScript carregam sem erros
- [ ] Imagens e assets estáticos funcionam
- [ ] Login funciona e redireciona corretamente
- [ ] Dashboard é acessível após login
- [ ] Logs não apresentam erros críticos

#### 4.3.2 Verificar Logs
```bash
tail -f /var/www/klgestorpub-[nome-instancia]/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

---

## 5. Solução de Problemas Básicos

### 5.1 Erro 404 - Página Não Encontrada

**Sintomas:**
- Página inicial retorna 404
- Assets não carregam

**Soluções:**
```bash
# Verificar configuração do Nginx
nginx -t

# Verificar permissões
ls -la /var/www/klgestorpub-[nome-instancia]/public/

# Corrigir permissões se necessário
chown -R www-data:www-data /var/www/klgestorpub-[nome-instancia]/
chmod -R 755 /var/www/klgestorpub-[nome-instancia]/
```

### 5.2 Erro 500 - Erro Interno do Servidor

**Sintomas:**
- Página retorna erro 500
- Aplicação não carrega

**Soluções:**
```bash
# Verificar logs do Laravel
tail -f /var/www/klgestorpub-[nome-instancia]/storage/logs/laravel.log

# Verificar logs do Nginx
tail -f /var/log/nginx/error.log

# Limpar caches
cd /var/www/klgestorpub-[nome-instancia]
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Verificar permissões de storage
chmod -R 775 storage bootstrap/cache
```

### 5.3 Erro de Conexão com Banco

**Sintomas:**
- Erro "Connection refused"
- Páginas não carregam dados

**Soluções:**
```bash
# Verificar configurações do .env
cat /var/www/klgestorpub-[nome-instancia]/.env | grep DB_

# Testar conexão manual
mysql -u klgestor_[nome-instancia] -p klgestorpub_[nome-instancia]

# Verificar se o usuário existe
mysql -u root -p
SELECT User, Host FROM mysql.user WHERE User LIKE 'klgestor_%';
```

### 5.4 Redirecionamento Incorreto Após Login

**Sintomas:**
- Login redireciona para URL incorreta
- Usuário não consegue acessar dashboard

**Soluções:**
```bash
# Verificar configuração do LoginController
nano /var/www/klgestorpub-[nome-instancia]/app/Http/Controllers/Auth/LoginController.php

# Garantir que $redirectTo = '/dashboard'
# Limpar caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 5.5 Assets Não Carregam (CSS/JS)

**Sintomas:**
- Página sem estilização
- JavaScript não funciona

**Soluções:**
```bash
# Verificar se os arquivos existem
ls -la /var/www/klgestorpub-[nome-instancia]/public/css/
ls -la /var/www/klgestorpub-[nome-instancia]/public/js/

# Compilar assets se necessário
cd /var/www/klgestorpub-[nome-instancia]
npm install
npm run build

# Verificar configuração do Nginx para assets
nginx -t
systemctl reload nginx
```

---

## 6. Manutenção e Monitoramento

### 6.1 Comandos de Manutenção Regular

```bash
# Limpeza de caches (executar semanalmente)
cd /var/www/klgestorpub-[nome-instancia]
php artisan optimize:clear
php artisan optimize

# Limpeza de logs antigos (executar mensalmente)
find storage/logs/ -name "*.log" -mtime +30 -delete

# Backup do banco de dados (executar diariamente)
mysqldump -u klgestor_[nome-instancia] -p klgestorpub_[nome-instancia] > backup_$(date +%Y%m%d).sql
```

### 6.2 Monitoramento de Performance

```bash
# Verificar uso de espaço
du -sh /var/www/klgestorpub-[nome-instancia]/

# Verificar logs de erro
tail -f /var/www/klgestorpub-[nome-instancia]/storage/logs/laravel.log

# Monitorar conexões de banco
mysql -u root -p -e "SHOW PROCESSLIST;"
```

---

## 7. Checklist Final

### 7.1 Pré-Deploy
- [ ] Banco de dados criado e configurado
- [ ] Arquivo .env configurado corretamente
- [ ] Migrações executadas
- [ ] Seeds executados
- [ ] Permissões configuradas
- [ ] Nginx configurado e testado

### 7.2 Pós-Deploy
- [ ] Página inicial acessível
- [ ] Login funcionando
- [ ] Dashboard acessível
- [ ] Assets carregando
- [ ] Logs sem erros críticos
- [ ] Backup configurado

### 7.3 Segurança
- [ ] APP_DEBUG=false em produção
- [ ] Senhas seguras configuradas
- [ ] SSL funcionando
- [ ] Permissões de arquivo adequadas
- [ ] Logs de auditoria ativos

---

## 8. Contatos e Suporte

Para suporte técnico ou dúvidas sobre este guia:

- **Documentação Técnica**: Consulte `Documentacao_Tecnica_Completa.md`
- **Logs do Sistema**: `/var/www/klgestorpub-[nome-instancia]/storage/logs/`
- **Configurações**: `/var/www/klgestorpub-[nome-instancia]/.env`

---

**Versão do Documento**: 1.0  
**Última Atualização**: Janeiro 2025  
**Sistema**: SISGERP v1.4.0  
**Compatibilidade**: Laravel 11.31, PHP 8.2+, MySQL 8.0+
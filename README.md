# KLGestorPub - Sistema de Gestão Pública

## Sobre o Projeto
Sistema de gestão pública para controle de receitas e despesas municipais, com categorização hierárquica e geração de relatórios.

## Tecnologias Utilizadas
- PHP 8.2.12
- Laravel 11.38.2
- MySQL/MariaDB
- Bootstrap 5
- Font Awesome
- jQuery
- Chart.js
- Laravel Excel (Maatwebsite)
- DomPDF

## Requisitos do Sistema
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Extensões PHP:
  - BCMath
  - Ctype
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - GD

## Instalação

### Windows (XAMPP)

1. Instale o XAMPP com PHP 8.2:
   - Baixe o XAMPP em https://www.apachefriends.org/
   - Execute o instalador e selecione PHP 8.2
   - Instale no diretório padrão (C:\xampp)

2. Instale o Composer:
   - Baixe o instalador em https://getcomposer.org/download/
   - Execute o instalador e use as configurações padrão
   - Verifique a instalação: `composer -V`

3. Clone o repositório:
   ```bash
   cd C:\xampp\htdocs
   git clone [url-do-repositorio] KLGestorPub
   cd KLGestorPub
   ```

4. Instale as dependências:
   ```bash
   composer install
   ```

5. Configure o ambiente:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

6. Configure o banco de dados:
   - Abra o phpMyAdmin (http://localhost/phpmyadmin)
   - Crie um novo banco de dados
   - Edite o arquivo .env com as credenciais

7. Execute as migrações:
   ```bash
   php artisan migrate
   php artisan db:seed --class=UserSeeder
   ```

8. Configure o Apache:
   - Abra C:\xampp\apache\conf\extra\httpd-vhosts.conf
   - Adicione:
     ```apache
     <VirtualHost *:80>
         DocumentRoot "C:/xampp/htdocs/KLGestorPub/public"
         ServerName klgestorpub.local
         <Directory "C:/xampp/htdocs/KLGestorPub/public">
             Options Indexes FollowSymLinks
             AllowOverride All
             Require all granted
         </Directory>
     </VirtualHost>
     ```

9. Configure o hosts:
   - Abra C:\Windows\System32\drivers\etc\hosts como administrador
   - Adicione: `127.0.0.1 klgestorpub.local`

10. Reinicie o Apache no XAMPP Control Panel

### Ubuntu Server (VPS)

1. Atualize o sistema:
   ```bash
   sudo apt update
   sudo apt upgrade
   ```

2. Instale as dependências:
   ```bash
   sudo apt install apache2 mysql-server php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-fpm unzip git
   ```

3. Instale o Composer:
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

4. Configure o MySQL:
   ```bash
   sudo mysql_secure_installation
   sudo mysql
   CREATE DATABASE klgestorpub;
   CREATE USER 'klgestorpub'@'localhost' IDENTIFIED BY 'sua_senha';
   GRANT ALL PRIVILEGES ON klgestorpub.* TO 'klgestorpub'@'localhost';
   FLUSH PRIVILEGES;
   exit;
   ```

5. Clone e configure o projeto:
   ```bash
   cd /var/www
   sudo git clone [url-do-repositorio] klgestorpub
   cd klgestorpub
   sudo chown -R www-data:www-data .
   sudo chmod -R 755 .
   sudo -u www-data composer install
   sudo -u www-data cp .env.example .env
   sudo -u www-data php artisan key:generate
   ```

6. Configure o .env:
   ```bash
   sudo nano .env
   # Atualize as configurações do banco de dados
   ```

7. Execute as migrações:
   ```bash
   sudo -u www-data php artisan migrate
   sudo -u www-data php artisan db:seed --class=UserSeeder
   ```

8. Configure o Apache:
   ```bash
   sudo nano /etc/apache2/sites-available/klgestorpub.conf
   ```
   Adicione:
   ```apache
   <VirtualHost *:80>
       ServerName seu-dominio.com
       DocumentRoot /var/www/klgestorpub/public
       
       <Directory /var/www/klgestorpub/public>
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/klgestorpub-error.log
       CustomLog ${APACHE_LOG_DIR}/klgestorpub-access.log combined
   </VirtualHost>
   ```

9. Ative o site e módulos:
   ```bash
   sudo a2ensite klgestorpub
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

### cPanel (Hostgator)

1. Acesse o cPanel:
   - Faça login no cPanel da sua hospedagem
   - Procure a seção "Arquivos"

2. Crie um subdomínio (opcional):
   - Clique em "Subdomínios"
   - Crie um subdomínio para o sistema
   - Aponte para a pasta public_html/klgestorpub/public

3. Upload dos arquivos:
   - Use o Gerenciador de Arquivos do cPanel
   - Navegue até public_html
   - Crie uma pasta klgestorpub
   - Faça upload dos arquivos do projeto
   - Certifique-se que a pasta public está dentro de klgestorpub

4. Configure o banco de dados:
   - No cPanel, vá para "MySQL Databases"
   - Crie um novo banco de dados
   - Crie um novo usuário
   - Adicione o usuário ao banco de dados com todos os privilégios

5. Configure o arquivo .env:
   - Renomeie .env.example para .env
   - Atualize as configurações do banco de dados
   - Configure a URL do aplicativo

6. Instale as dependências:
   - Acesse via SSH (se disponível):
     ```bash
     cd public_html/klgestorpub
     composer install --no-dev
     ```
   - Ou use o Composer do cPanel (Softaculous)

7. Configure as permissões:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

8. Execute as migrações:
   - Se tiver acesso SSH:
     ```bash
     php artisan migrate
     php artisan db:seed --class=UserSeeder
     ```
   - Ou importe o SQL via phpMyAdmin

9. Configure o .htaccess na raiz:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ public/$1 [L]
   </IfModule>
   ```

10. Otimize para produção:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

## Estrutura do Projeto

### Models
- `User.php`: Gerenciamento de usuários e autenticação
- `Category.php`: Categorias hierárquicas (Fonte, Bloco, Grupo, Ação)
- `Revenue.php`: Registro de receitas
- `Expense.php`: Registro de despesas
- `ExpenseClassification.php`: Classificação de despesas
- `CitySetting.php`: Configurações da cidade
- `AuditLog.php`: Logs de auditoria

### Controllers
- `UserController.php`: CRUD de usuários
- `CategoryController.php`: CRUD de categorias
- `RevenueController.php`: CRUD de receitas
- `ExpenseController.php`: CRUD de despesas
- `ExpenseClassificationController.php`: CRUD de classificações
- `CitySettingsController.php`: Gestão de dados da cidade
- `ReportController.php`: Geração de relatórios
- `AuditLogController.php`: Visualização de logs
- `DocumentationController.php`: Documentação do sistema

### Middleware
- `AdminMiddleware.php`: Restrição de acesso admin
- `OperatorMiddleware.php`: Restrição de acesso operador

### Views
- `layouts/`: Templates base
- `auth/`: Autenticação
- `categories/`: Gestão de categorias
- `revenues/`: Gestão de receitas
- `expenses/`: Gestão de despesas
- `reports/`: Relatórios
- `settings/`: Configurações
- `audit/`: Logs de auditoria
- `documentation/`: Documentação

### Rotas
- Autenticação: `auth/`
- Dashboard: `/dashboard`
- Categorias: `categories/`
- Receitas: `revenues/`
- Despesas: `expenses/`
- Relatórios: `reports/`
- Configurações: `settings/`
- Auditoria: `audit/`
- Documentação: `documentation/`

## Funcionalidades Principais

### Categorização Hierárquica
- Fonte (nível 1)
- Bloco (nível 2)
- Grupo (nível 3)
- Ação (nível 4)

### Gestão de Receitas e Despesas
- CRUD completo
- Seleção dinâmica de categorias
- Validações
- Auditoria de alterações

### Relatórios
- Balanço financeiro
- Receitas por período
- Despesas por período
- Exportação PDF/Excel
- Gráficos interativos

### Administração
- Gestão de usuários
- Configurações da cidade
- Logs de auditoria
- Controle de permissões

## Segurança
- Autenticação de usuários
- Middleware de proteção
- Validação de formulários
- Logs de atividades
- Conformidade LGPD

## Contribuição
1. Fork o projeto
2. Crie sua branch de feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença
Este projeto está sob a licença [sua-licenca].

## Suporte
Para suporte, envie um email para [seu-email].

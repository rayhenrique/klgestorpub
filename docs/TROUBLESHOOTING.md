# 🔧 KL Gestor Pub - Guia de Troubleshooting v1.4.0

Este documento contém soluções para problemas comuns encontrados no KL Gestor Pub v1.4.0.

## 🚨 Problemas Críticos

### 1. Aplicação Não Carrega (Erro 500)

**Sintomas:**
- Página em branco ou erro 500
- Logs mostram erros PHP

**Soluções:**
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Verificar permissões
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Recompilar assets
npm run build
```

### 2. Banco de Dados Não Conecta

**Sintomas:**
- Erro "Connection refused"
- Página de login não carrega

**Soluções:**
```bash
# Verificar status do MySQL
sudo systemctl status mysql

# Reiniciar MySQL
sudo systemctl restart mysql

# Verificar credenciais no .env
DB_HOST=localhost
DB_DATABASE=klgestorpub
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();
```

### 3. Sistema de Backup Falha

**Sintomas:**
- Erro ao criar backup
- Download não funciona
- Restauração falha

**Soluções:**
```bash
# Verificar permissões do diretório
chmod -R 775 storage/app/backups
chown -R www-data:www-data storage/app/backups

# Verificar espaço em disco
df -h

# Testar backup manual
php artisan backup:database

# Verificar logs de backup
tail -f storage/logs/laravel.log | grep -i backup
```

## 📱 Problemas de Responsividade

### 1. Layout Quebrado no Mobile

**Sintomas:**
- Elementos sobrepostos
- Sidebar não funciona
- Tabelas cortadas

**Soluções:**
```bash
# Limpar cache do navegador
# Ctrl+F5 ou Cmd+Shift+R

# Recompilar CSS
npm run build

# Verificar viewport no HTML
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

### 2. Menu Hamburger Não Funciona

**Sintomas:**
- Botão não responde
- Sidebar não abre/fecha

**Soluções:**
```javascript
// Verificar console do navegador (F12)
// Procurar erros JavaScript

// Recarregar assets
npm run build

// Verificar se jQuery está carregado
console.log(typeof jQuery);
```

## 🐳 Problemas Docker

### 1. Container Não Inicia

**Sintomas:**
- `docker-compose up` falha
- Container para imediatamente

**Soluções:**
```bash
# Verificar logs do container
docker-compose logs app

# Reconstruir imagem
docker-compose build --no-cache

# Verificar portas em uso
netstat -tulpn | grep :8080

# Limpar volumes órfãos
docker system prune -f
```

### 2. Banco Docker Não Conecta

**Sintomas:**
- App não conecta ao MySQL container
- Erro de conexão recusada

**Soluções:**
```bash
# Verificar se containers estão na mesma rede
docker network ls
docker network inspect klgestorpub_default

# Verificar variáveis de ambiente
docker-compose exec app env | grep DB_

# Testar conexão entre containers
docker-compose exec app ping mysql
```

### 3. Volumes Docker Perdidos

**Sintomas:**
- Dados perdidos após restart
- Uploads desaparecem

**Soluções:**
```bash
# Verificar volumes
docker volume ls

# Inspecionar volume
docker volume inspect klgestorpub_mysql_data

# Backup de volume
docker run --rm -v klgestorpub_mysql_data:/data -v $(pwd):/backup ubuntu tar czf /backup/mysql_backup.tar.gz /data
```

## 🔐 Problemas de Autenticação

### 1. Não Consegue Fazer Login

**Sintomas:**
- Credenciais corretas mas login falha
- Redirecionamento infinito

**Soluções:**
```bash
# Verificar sessões
php artisan session:table
php artisan migrate

# Limpar sessões
php artisan cache:clear

# Verificar configuração de sessão no .env
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Recriar usuário admin
php artisan tinker
>>> User::where('email', 'admin@admin.com')->first()->update(['password' => Hash::make('admin')]);
```

### 2. Sessão Expira Rapidamente

**Sintomas:**
- Logout automático frequente
- Perda de dados em formulários

**Soluções:**
```bash
# Aumentar tempo de sessão no .env
SESSION_LIFETIME=480

# Verificar configuração do Redis (se usado)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Limpar cache de configuração
php artisan config:cache
```

## 💾 Problemas de Performance

### 1. Aplicação Lenta

**Sintomas:**
- Páginas demoram para carregar
- Relatórios lentos

**Soluções:**
```bash
# Habilitar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimizar autoloader
composer dump-autoload --optimize

# Verificar queries lentas
# Habilitar query log no .env
DB_LOG_QUERIES=true

# Verificar uso de memória
free -h
htop
```

### 2. Banco de Dados Lento

**Sintomas:**
- Queries demoram muito
- Timeout em relatórios

**Soluções:**
```sql
-- Verificar queries lentas
SHOW PROCESSLIST;

-- Verificar índices
SHOW INDEX FROM revenues;
SHOW INDEX FROM expenses;

-- Otimizar tabelas
OPTIMIZE TABLE revenues, expenses, categories;

-- Analisar queries
EXPLAIN SELECT * FROM revenues WHERE created_at >= '2024-01-01';
```

## 📊 Problemas de Relatórios

### 1. Relatório PDF Não Gera

**Sintomas:**
- Erro ao gerar PDF
- Download falha

**Soluções:**
```bash
# Verificar extensão PHP
php -m | grep -i gd
php -m | grep -i mbstring

# Instalar dependências faltantes
sudo apt-get install php-gd php-mbstring

# Verificar permissões de escrita
chmod -R 775 storage/app/reports

# Testar geração manual
php artisan tinker
>>> app('dompdf.wrapper')->loadHTML('<h1>Test</h1>')->save(storage_path('app/test.pdf'));
```

### 2. Relatório Excel Corrompido

**Sintomas:**
- Arquivo não abre
- Dados incorretos

**Soluções:**
```bash
# Verificar extensão PHP
php -m | grep -i zip

# Instalar php-zip se necessário
sudo apt-get install php-zip

# Limpar cache
php artisan cache:clear

# Verificar encoding
# Certificar que dados estão em UTF-8
```

## 🌐 Problemas de Rede

### 1. Assets Não Carregam

**Sintomas:**
- CSS/JS não carrega
- Imagens quebradas

**Soluções:**
```bash
# Verificar URL no .env
APP_URL=https://seudominio.com

# Recompilar assets
npm run build

# Verificar permissões
chmod -R 755 public/

# Verificar configuração do Nginx
# Verificar se serve arquivos estáticos
location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### 2. HTTPS Não Funciona

**Sintomas:**
- Certificado inválido
- Mixed content warnings

**Soluções:**
```bash
# Renovar certificado Let's Encrypt
sudo certbot renew

# Verificar configuração no .env
APP_URL=https://seudominio.com
FORCE_HTTPS=true

# Verificar configuração do Nginx
# Certificar que redireciona HTTP para HTTPS
return 301 https://$server_name$request_uri;
```

## 🛠️ Comandos de Diagnóstico

### Verificação Completa do Sistema
```bash
#!/bin/bash
echo "=== KL Gestor Pub - Diagnóstico do Sistema ==="

echo "\n1. Status dos Serviços:"
sudo systemctl status nginx mysql php8.2-fpm redis --no-pager

echo "\n2. Uso de Recursos:"
free -h
df -h

echo "\n3. Logs Recentes:"
tail -n 20 storage/logs/laravel.log

echo "\n4. Configuração Laravel:"
php artisan about

echo "\n5. Status do Banco:"
php artisan tinker --execute="echo 'DB OK: ' . DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);"

echo "\n6. Permissões:"
ls -la storage/ bootstrap/cache/

echo "\n=== Diagnóstico Concluído ==="
```

### Script de Limpeza
```bash
#!/bin/bash
echo "=== Limpeza do Sistema ==="

# Limpar caches Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Limpar logs antigos (mais de 30 dias)
find storage/logs/ -name "*.log" -mtime +30 -delete

# Limpar sessões expiradas
php artisan session:gc

# Otimizar banco
php artisan optimize

echo "Limpeza concluída!"
```

## 📞 Quando Buscar Ajuda

### Antes de Reportar um Problema:
1. ✅ Verificar logs de erro
2. ✅ Tentar soluções deste guia
3. ✅ Verificar se é problema conhecido no CHANGELOG
4. ✅ Testar em ambiente limpo

### Informações para Incluir no Reporte:
- **Versão**: KL Gestor Pub v1.4.0
- **Ambiente**: Produção/Desenvolvimento/Docker
- **Sistema Operacional**: Ubuntu 20.04, etc.
- **Logs de erro**: Últimas 50 linhas relevantes
- **Passos para reproduzir**: Sequência exata
- **Comportamento esperado vs atual**

### Contato:
- **Email**: rayhenrique@gmail.com
- **Assunto**: [KL Gestor Pub v1.4.0] Descrição do problema

---

**KL Gestor Pub v1.4.0** - Troubleshooting Guide  
**Última atualização**: Janeiro 2025  
**Desenvolvido por**: KL Tecnologia
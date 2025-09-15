# 🐳 Docker Configuration - KL Gestor Pub

Este documento descreve como executar o KL Gestor Pub usando Docker, proporcionando um ambiente de desenvolvimento e produção consistente e isolado.

## 📋 Pré-requisitos

- **Docker Desktop** (Windows/Mac) ou **Docker Engine** (Linux)
- **Docker Compose** v2.0 ou superior
- **Git** para clonar o repositório
- **4GB RAM** mínimo disponível para containers
- **10GB** de espaço em disco livre

### Verificação dos Pré-requisitos

```bash
# Verificar Docker
docker --version
docker-compose --version

# Verificar se Docker está rodando
docker info
```

## 🚀 Início Rápido

### 1. Configuração Inicial

**Windows:**
```cmd
# Executar setup inicial
docker-setup.bat

# Iniciar aplicação
docker-start.bat
```

**Linux/Mac:**
```bash
# Dar permissões aos scripts
chmod +x docker-*.sh

# Executar setup inicial
./docker-setup.sh

# Iniciar aplicação
./docker-start.sh
```

### 2. Acessar a Aplicação

- **Aplicação Principal:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081 (desenvolvimento)
- **Mailhog:** http://localhost:8025 (desenvolvimento)
- **Health Check:** http://localhost:8080/health

## 🏗️ Arquitetura dos Containers

### Serviços Principais

| Serviço | Porta | Descrição |
|---------|-------|----------|
| **app** | - | Aplicação Laravel (PHP 8.2-FPM) |
| **nginx** | 8080, 8443 | Servidor web Nginx |
| **mysql** | 3306 | Banco de dados MySQL 8.0 |
| **redis** | 6379 | Cache e sessões Redis |

### Serviços de Desenvolvimento

| Serviço | Porta | Descrição |
|---------|-------|----------|
| **node** | - | Compilação de assets |
| **mailhog** | 8025, 1025 | Servidor de email para testes |
| **phpmyadmin** | 8081 | Interface web para MySQL |

## 📁 Estrutura de Arquivos Docker

```
klgestorpub/
├── Dockerfile                     # Imagem principal da aplicação
├── docker-compose.yml             # Configuração dos serviços
├── docker-compose.prod.yml        # Override para produção
├── .dockerignore                  # Arquivos ignorados no build
├── .env.docker                    # Variáveis de ambiente Docker
├── docker-start.sh/.bat           # Script de inicialização
├── docker-setup.sh/.bat           # Script de configuração
├── docker-build.sh/.bat           # Script de build
├── docker-permissions.ps1         # Configuração Windows
└── deployment/docker/             # Configurações dos serviços
    ├── nginx/
    │   ├── default.conf           # Configuração Nginx
    │   └── nginx.conf             # Configuração global Nginx
    ├── php/
    │   ├── php.ini                # Configuração PHP
    │   └── xdebug.ini             # Configuração Xdebug (dev)
    ├── mysql/
    │   ├── init.sql               # Inicialização do banco
    │   └── my.cnf                 # Configuração MySQL
    ├── redis/
    │   └── redis.conf             # Configuração Redis
    └── supervisor/
        └── supervisord.conf       # Configuração Supervisor
```

## ⚙️ Configuração Detalhada

### Variáveis de Ambiente

O arquivo `.env.docker` contém as configurações específicas para Docker:

```env
# Aplicação
APP_URL=http://localhost:8080
APP_ENV=local
APP_DEBUG=true

# Banco de Dados
DB_HOST=mysql
DB_DATABASE=klgestorpub
DB_USERNAME=klgestorpub
DB_PASSWORD=klgestorpub_password

# Cache e Sessões
REDIS_HOST=redis
CACHE_STORE=redis
SESSION_DRIVER=redis

# Email (Desenvolvimento)
MAIL_HOST=mailhog
MAIL_PORT=1025
```

### Volumes Persistentes

- **mysql_data:** Dados do banco MySQL
- **redis_data:** Dados do Redis
- **storage:** Arquivos de storage do Laravel
- **logs:** Logs da aplicação e serviços

## 🔧 Comandos Úteis

### Gerenciamento de Containers

```bash
# Iniciar todos os serviços
docker-compose up -d

# Iniciar com logs visíveis
docker-compose up

# Parar todos os serviços
docker-compose down

# Reiniciar um serviço específico
docker-compose restart app

# Ver status dos containers
docker-compose ps

# Ver logs de um serviço
docker-compose logs -f app
```

### Comandos Laravel

```bash
# Executar comandos Artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear

# Acessar shell do container
docker-compose exec app bash

# Executar Composer
docker-compose exec app composer install

# Executar NPM
docker-compose exec app npm install
docker-compose exec app npm run build
```

### Banco de Dados

```bash
# Acessar MySQL
docker-compose exec mysql mysql -u klgestorpub -p klgestorpub

# Backup do banco
docker-compose exec mysql mysqldump -u klgestorpub -p klgestorpub > backup.sql

# Restaurar backup
docker-compose exec -T mysql mysql -u klgestorpub -p klgestorpub < backup.sql
```

## 🌍 Ambientes

### Desenvolvimento

```bash
# Iniciar com serviços de desenvolvimento
docker-compose --profile development up -d

# Ou usar o script
./docker-start.sh development
```

**Recursos inclusos:**
- Xdebug habilitado
- Mailhog para testes de email
- phpMyAdmin para gerenciar banco
- Hot reload para assets
- Logs detalhados

### Produção

```bash
# Build para produção
./docker-build.sh production

# Iniciar em produção
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Ou usar o script
./docker-start.sh production
```

**Otimizações incluídas:**
- Imagem otimizada sem ferramentas de desenvolvimento
- Caches do Laravel pré-compilados
- Configurações de performance
- Logs reduzidos
- Segurança aprimorada

## 🔍 Monitoramento e Logs

### Visualizar Logs

```bash
# Logs de todos os serviços
docker-compose logs -f

# Logs de um serviço específico
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql

# Logs do Laravel
docker-compose exec app tail -f storage/logs/laravel.log

# Logs do Nginx
docker-compose exec nginx tail -f /var/log/nginx/klgestorpub_access.log
```

### Health Checks

```bash
# Verificar saúde dos containers
docker-compose ps

# Health check da aplicação
curl http://localhost:8080/health

# Verificar conectividade do banco
docker-compose exec app php artisan tinker --execute="DB::connection()->getPdo();"
```

## 🛠️ Troubleshooting

### Problemas Comuns

#### Container não inicia
```bash
# Verificar logs de erro
docker-compose logs app

# Reconstruir imagem
docker-compose build --no-cache app
```

#### Erro de permissões (Linux/Mac)
```bash
# Corrigir permissões
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

#### Banco de dados não conecta
```bash
# Verificar se MySQL está rodando
docker-compose exec mysql mysqladmin ping

# Recriar volume do banco
docker-compose down -v
docker-compose up -d
```

#### Porta já em uso
```bash
# Verificar processos usando a porta
netstat -tulpn | grep :8080

# Alterar porta no docker-compose.yml
ports:
  - "8081:80"  # Usar porta 8081 em vez de 8080
```

### Limpeza do Sistema

```bash
# Parar e remover containers
docker-compose down

# Remover volumes (CUIDADO: apaga dados!)
docker-compose down -v

# Limpar imagens não utilizadas
docker system prune -f

# Limpeza completa
docker system prune -a -f
```

## 🔒 Segurança

### Produção

1. **Alterar senhas padrão:**
   ```env
   DB_PASSWORD=sua_senha_segura
   MYSQL_ROOT_PASSWORD=root_senha_segura
   ```

2. **Usar HTTPS:**
   - Configurar certificados SSL
   - Atualizar `APP_URL` para HTTPS

3. **Restringir acesso:**
   - Remover phpMyAdmin em produção
   - Configurar firewall
   - Usar redes Docker isoladas

### Backup

```bash
# Script de backup automático
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker-compose exec -T mysql mysqldump -u klgestorpub -p klgestorpub > "backup_${DATE}.sql"
tar -czf "klgestorpub_backup_${DATE}.tar.gz" storage/ backup_${DATE}.sql
```

## 📚 Recursos Adicionais

- [Documentação Docker](https://docs.docker.com/)
- [Docker Compose Reference](https://docs.docker.com/compose/)
- [Laravel Docker Best Practices](https://laravel.com/docs/deployment#docker)
- [Nginx Configuration](https://nginx.org/en/docs/)

## 🆘 Suporte

Para problemas específicos do Docker:

1. Verificar logs dos containers
2. Consultar este documento
3. Verificar issues no repositório
4. Contatar a equipe de desenvolvimento

---

**Desenvolvido por KL Tecnologia**  
**Versão:** 1.4.0  
**Última atualização:** Janeiro 2025
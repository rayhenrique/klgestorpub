# 🏗️ KL Gestor Pub - Infrastructure Documentation v1.4.0

Este diretório contém arquivos e configurações relacionados à infraestrutura do projeto KL Gestor Pub, incluindo monitoramento, logging, backup e configurações de produção.

## 🎯 Visão Geral da Infraestrutura

### Arquitetura v1.4.0
- **Aplicação Laravel** com PHP 8.2-FPM
- **Nginx** como servidor web e proxy reverso
- **MySQL 8.0** para banco de dados principal
- **Redis** para cache e sessões
- **Docker** para containerização (opcional)
- **Supervisor** para gerenciamento de filas
- **Sistema de Backup** integrado

### Componentes de Monitoramento
- **Grafana** - Dashboards e visualização de métricas
- **Prometheus** - Coleta de métricas do sistema
- **Portainer** - Gerenciamento de containers Docker
- **Logs centralizados** - Agregação de logs de todos os serviços

## 📁 Estrutura de Diretórios

### `/logs/`
- **Logs da aplicação Laravel** (`laravel.log`)
- **Logs do Nginx** (`access.log`, `error.log`)
- **Logs do MySQL** (`mysql-error.log`, `mysql-slow.log`)
- **Logs do sistema de backup** (`backup.log`)
- **Logs de workers/filas** (`worker.log`)
- **IMPORTANTE**: Não incluído no controle de versão

### `/secrets/`
- **Certificados SSL** (Let's Encrypt)
- **Chaves de API** e tokens de acesso
- **Senhas de serviços** (Portainer, Grafana, etc.)
- **Arquivos de configuração sensíveis**
- **CRÍTICO**: Nunca commitar no Git - configurado no `.gitignore`

### `/volumes/`
- **Dados do Grafana** (`grafana_data/`)
- **Dados do Portainer** (`portainer_data/`)
- **Dados do Prometheus** (`prometheus_data/`)
- **Dados do Redis** (`redis_data/`)
- **Backups do banco de dados** (`mysql_backups/`)
- **IMPORTANTE**: Não incluído no controle de versão

### `/monitoring/`
- **Configurações do Grafana** (`grafana/`)
- **Configurações do Prometheus** (`prometheus/`)
- **Dashboards personalizados** (`dashboards/`)
- **Alertas e notificações** (`alerts/`)

### `/backup/`
- **Scripts de backup automatizado**
- **Configurações de retenção**
- **Logs de backup e restauração**
- **Arquivos de backup temporários**

## 🔧 Configuração e Deploy

### Pré-requisitos
```bash
# Instalar Docker e Docker Compose
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### Inicialização da Infraestrutura
```bash
# Criar diretórios necessários
mkdir -p infrastructure/{logs,secrets,volumes,monitoring,backup}

# Configurar permissões
sudo chown -R $USER:docker infrastructure/
chmod -R 755 infrastructure/

# Iniciar serviços de monitoramento
docker-compose -f infrastructure/docker-compose.monitoring.yml up -d
```

## 📊 Monitoramento e Métricas

### Grafana Dashboards
- **Dashboard Principal**: Visão geral do sistema
- **Performance da Aplicação**: Métricas Laravel
- **Infraestrutura**: CPU, RAM, Disco, Rede
- **Banco de Dados**: Queries, conexões, performance
- **Backup System**: Status e histórico de backups

### Métricas Coletadas
- **Sistema**: CPU, RAM, Disco, Rede
- **Aplicação**: Response time, requests/sec, errors
- **Banco**: Query time, connections, slow queries
- **Cache**: Hit rate, memory usage (Redis)
- **Backup**: Success rate, duration, file sizes

### Alertas Configurados
- **Alto uso de CPU** (>80% por 5 min)
- **Pouco espaço em disco** (<10% livre)
- **Falha no backup** (backup não executado em 24h)
- **Erro na aplicação** (>10 erros/min)
- **Banco de dados offline**

## 🔒 Segurança e Backup

### Políticas de Backup
- **Backup diário** do banco de dados (2:00 AM)
- **Backup semanal** completo (domingo 1:00 AM)
- **Retenção**: 30 dias para backups diários, 12 semanas para semanais
- **Verificação de integridade** automática
- **Backup offsite** (opcional - configurar S3/Google Cloud)

### Segurança
- **Firewall UFW** configurado
- **SSL/TLS** obrigatório (Let's Encrypt)
- **Logs de auditoria** para todas as operações
- **Acesso restrito** aos diretórios sensíveis
- **Rotação de logs** automática

## 🚨 Troubleshooting

### Comandos Úteis
```bash
# Verificar status dos serviços
sudo systemctl status nginx mysql php8.2-fpm redis

# Verificar logs em tempo real
sudo tail -f infrastructure/logs/laravel.log
sudo tail -f infrastructure/logs/nginx-error.log

# Verificar uso de recursos
htop
df -h
free -h

# Verificar containers Docker
docker ps
docker stats
```

### Problemas Comuns
1. **Alto uso de memória**: Verificar queries lentas no MySQL
2. **Disco cheio**: Limpar logs antigos e backups
3. **Aplicação lenta**: Verificar cache Redis e otimizar queries
4. **Backup falhou**: Verificar permissões e espaço em disco

## 📞 Suporte e Manutenção

### Manutenção Preventiva
- **Atualização mensal** do sistema operacional
- **Limpeza de logs** semanalmente
- **Verificação de backups** diariamente
- **Monitoramento de performance** contínuo

### Contato
- **Email**: rayhenrique@gmail.com
- **Documentação**: Consultar `/docs/` para detalhes técnicos
- **Logs**: Sempre verificar logs antes de reportar problemas

---

**KL Gestor Pub v1.4.0** - Infrastructure Documentation  
**Última atualização**: Janeiro 2025  
**Desenvolvido por**: KL Tecnologia
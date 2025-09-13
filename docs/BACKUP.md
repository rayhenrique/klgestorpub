# Sistema de Backup e Restauração - KL Gestor Pub

## Visão Geral

O KL Gestor Pub agora inclui um sistema completo de backup e restauração do banco de dados, permitindo que administradores criem, gerenciem e restaurem backups de forma segura e eficiente.

## Funcionalidades

### 🔧 Comandos Artisan

#### Criar Backup
```bash
# Backup simples
php artisan backup:database

# Backup com compressão (recomendado)
php artisan backup:database --compress
```

#### Restaurar Backup
```bash
# Restaurar com confirmação interativa
php artisan backup:restore nome_do_arquivo.sql

# Restaurar sem confirmação (uso em scripts)
php artisan backup:restore nome_do_arquivo.sql --force
```

### 🌐 Interface Web

Acesse a interface web através do menu **Administração > Backup do Banco** (disponível apenas para administradores).

#### Funcionalidades da Interface:
- **Visualizar Backups**: Lista todos os backups disponíveis com informações de tamanho, data e tipo
- **Criar Backup**: Gera novos backups com um clique
- **Download**: Baixa arquivos de backup para armazenamento externo
- **Upload**: Envia arquivos de backup externos
- **Restaurar**: Restaura o banco de dados a partir de um backup
- **Deletar**: Remove backups desnecessários

### ⏰ Backup Automático

O sistema está configurado para criar backups automáticos diariamente às 02:00h. Para ativar o agendamento:

```bash
# Adicionar ao crontab (Linux/Mac)
* * * * * cd /caminho/para/projeto && php artisan schedule:run >> /dev/null 2>&1

# Ou usar o Supervisor/Task Scheduler no Windows
```

## Tipos de Backup

### 🟢 Automático
Backups criados pelo sistema automaticamente ou via comando Artisan.

### 🟡 Pré-Restauração
Backups de segurança criados automaticamente antes de cada restauração.

### 🔵 Enviado
Backups enviados através da interface web.

## Segurança e Boas Práticas

### 🔒 Controle de Acesso
- Apenas usuários com privilégios de administrador podem acessar as funcionalidades de backup
- Todas as operações são registradas nos logs de auditoria
- Validação rigorosa de arquivos de backup

### 💾 Armazenamento
- Backups são armazenados em `storage/app/backups/`
- Compressão automática reduz o tamanho dos arquivos em até 80%
- Limpeza automática de backups antigos (mantém últimos 30 dias)

### 🛡️ Backup de Segurança
- Antes de cada restauração, um backup automático é criado
- Permite reverter mudanças em caso de problemas
- Identificado com prefixo `pre_restore_backup_`

## Formatos Suportados

- **`.sql`**: Arquivos SQL não comprimidos
- **`.sql.gz`**: Arquivos SQL comprimidos (recomendado)

## Estrutura dos Arquivos

### Nomenclatura
```
backup_[database]_[YYYY-MM-DD_HH-mm-ss].sql[.gz]
pre_restore_backup_[database]_[YYYY-MM-DD_HH-mm-ss].sql
uploaded_[nome_original]_[YYYY-MM-DD_HH-mm-ss].sql[.gz]
```

### Conteúdo do Backup
- Estrutura completa das tabelas (CREATE TABLE)
- Todos os dados das tabelas (INSERT)
- Configurações de chaves estrangeiras
- Metadados e comentários

## Monitoramento e Logs

### 📊 Logs de Auditoria
Todas as operações de backup são registradas:
- Criação de backups
- Restaurações realizadas
- Downloads de arquivos
- Uploads de backups
- Exclusões de arquivos

### 🔍 Localização dos Logs
- **Laravel Logs**: `storage/logs/laravel.log`
- **Auditoria**: Interface web em "Logs de Auditoria"

## Solução de Problemas

### ❌ Problemas Comuns

#### "Arquivo de backup inválido"
- Verifique se o arquivo não está corrompido
- Confirme que é um arquivo SQL válido
- Teste com um backup recém-criado

#### "Erro de permissão"
- Verifique permissões da pasta `storage/app/backups/`
- Confirme que o usuário web tem acesso de escrita

#### "Falha na restauração"
- Verifique logs detalhados em `storage/logs/laravel.log`
- Confirme configurações do banco de dados
- Teste conectividade com o banco

### 🔧 Comandos de Diagnóstico

```bash
# Verificar permissões
ls -la storage/app/backups/

# Testar conexão com banco
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar logs recentes
tail -f storage/logs/laravel.log
```

## Configurações Avançadas

### 📁 Alterar Diretório de Backup
Edite o caminho em `DatabaseBackup.php` e `DatabaseRestore.php`:
```php
$backupPath = storage_path('app/backups'); // Altere aqui
```

### ⏱️ Alterar Horário do Backup Automático
Edite `routes/console.php`:
```php
Schedule::command('backup:database --compress')
    ->daily()
    ->at('02:00'); // Altere o horário aqui
```

### 🗂️ Alterar Retenção de Backups
Edite o método `cleanOldBackups()` em `DatabaseBackup.php`:
```php
$cutoffDate = Carbon::now()->subDays(30); // Altere os dias aqui
```

## API de Backup (Programática)

### Criar Backup via Código
```php
use Illuminate\Support\Facades\Artisan;

// Criar backup
$exitCode = Artisan::call('backup:database', ['--compress' => true]);

if ($exitCode === 0) {
    $output = Artisan::output();
    // Backup criado com sucesso
}
```

### Restaurar via Código
```php
// Restaurar backup
$exitCode = Artisan::call('backup:restore', [
    'filename' => 'backup_file.sql.gz',
    '--force' => true
]);
```

## Integração com Serviços Externos

### 🌐 Armazenamento em Nuvem
Para integrar com serviços como AWS S3, Google Drive, etc., edite os comandos para incluir upload automático após a criação do backup.

### 📧 Notificações
Implemente notificações por email em caso de falhas:
```php
// No método handle() dos comandos
if ($backupFailed) {
    Mail::to('admin@example.com')->send(new BackupFailedMail($error));
}
```

## Considerações de Performance

- **Bancos Grandes**: Para bancos > 1GB, considere usar backup incremental
- **Horário**: Execute backups em horários de baixo uso
- **Compressão**: Sempre use compressão para economizar espaço
- **Rede**: Para restaurações grandes, considere fazer upload local

## Suporte

Para suporte técnico ou dúvidas:
- Consulte os logs em `storage/logs/laravel.log`
- Verifique a documentação técnica em `docs/technical.md`
- Entre em contato com a equipe de desenvolvimento

---

**Versão**: 1.4.0  
**Última Atualização**: Setembro 2025  
**Compatibilidade**: Laravel 11, PHP 8.1+, MySQL 8.0+
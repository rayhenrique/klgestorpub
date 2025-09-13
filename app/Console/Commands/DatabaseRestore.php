<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DatabaseRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore {filename : The backup filename to restore} {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from a backup file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = $this->argument('filename');
        $backupPath = storage_path('app/backups');
        $fullPath = $backupPath . '/' . $filename;

        // Verificar se o arquivo existe
        if (!file_exists($fullPath)) {
            $this->error("Arquivo de backup não encontrado: {$filename}");
            return 1;
        }

        // Verificar se é um arquivo válido
        if (!$this->isValidBackupFile($fullPath)) {
            $this->error('Arquivo de backup inválido ou corrompido.');
            return 1;
        }

        $this->info("Arquivo de backup encontrado: {$filename}");
        $fileSize = $this->formatBytes(filesize($fullPath));
        $this->info("Tamanho: {$fileSize}");

        // Confirmação (a menos que --force seja usado)
        if (!$this->option('force')) {
            if (!$this->confirm('Esta operação irá substituir todos os dados atuais do banco. Deseja continuar?')) {
                $this->info('Operação cancelada.');
                return 0;
            }
        }

        try {
            // Criar backup automático antes da restauração
            $this->info('Criando backup de segurança antes da restauração...');
            $preRestoreBackup = $this->createPreRestoreBackup();
            
            if ($preRestoreBackup) {
                $this->info("Backup de segurança criado: {$preRestoreBackup}");
            }

            // Executar restauração
            $this->info('Iniciando restauração do banco de dados...');
            
            if ($this->restoreDatabase($fullPath)) {
                $this->info('Banco de dados restaurado com sucesso!');
                
                // Log da operação
                Log::info('Database restored', [
                    'filename' => $filename,
                    'pre_restore_backup' => $preRestoreBackup,
                    'restored_at' => Carbon::now()
                ]);
                
                return 0;
            } else {
                $this->error('Falha na restauração do banco de dados.');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Erro inesperado: ' . $e->getMessage());
            Log::error('Restore error', ['exception' => $e, 'filename' => $filename]);
            return 1;
        }
    }

    /**
     * Verificar se o arquivo é um backup válido
     */
    private function isValidBackupFile($filePath)
    {
        try {
            $content = '';
            
            // Se for arquivo comprimido, descomprimir para verificar
            if (pathinfo($filePath, PATHINFO_EXTENSION) === 'gz') {
                $compressedContent = file_get_contents($filePath);
                if ($compressedContent === false) {
                    return false;
                }
                
                $content = gzdecode($compressedContent);
                if ($content === false) {
                    return false;
                }
            } else {
                $content = file_get_contents($filePath);
                if ($content === false) {
                    return false;
                }
            }

            // Verificar se contém comandos SQL válidos
            $lines = explode("\n", $content);
            $lineCount = 0;
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && !str_starts_with($line, '--')) {
                    if (preg_match('/^(CREATE|INSERT|DROP|USE|SET|LOCK|UNLOCK)/i', $line)) {
                        return true;
                    }
                    $lineCount++;
                    if ($lineCount > 50) {
                        break;
                    }
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Criar backup antes da restauração
     */
    private function createPreRestoreBackup()
    {
        try {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "pre_restore_backup_{$database}_{$timestamp}.sql";
            $backupPath = storage_path('app/backups');
            $fullPath = $backupPath . '/' . $filename;

            // Usar PHP nativo para backup
            if ($this->createBackupWithPHP($fullPath, $host, $port, $database, $username, $password)) {
                return $filename;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Pre-restore backup failed', ['exception' => $e]);
            return null;
        }
    }

    /**
     * Restaurar o banco de dados
     */
    private function restoreDatabase($filePath)
    {
        try {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Ler conteúdo do arquivo
            $sqlContent = '';
            
            if (pathinfo($filePath, PATHINFO_EXTENSION) === 'gz') {
                // Arquivo comprimido - descomprimir
                $sqlContent = gzdecode(file_get_contents($filePath));
                if ($sqlContent === false) {
                    $this->error('Erro ao descomprimir arquivo.');
                    return false;
                }
            } else {
                // Arquivo normal
                $sqlContent = file_get_contents($filePath);
                if ($sqlContent === false) {
                    $this->error('Erro ao ler arquivo de backup.');
                    return false;
                }
            }

            // Conectar ao banco de dados
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]);

            // Dividir SQL em comandos individuais
            $commands = $this->splitSqlCommands($sqlContent);
            $totalCommands = count($commands);
            $this->info("Executando {$totalCommands} comandos SQL...");

            $executedCommands = 0;
            foreach ($commands as $command) {
                $command = trim($command);
                if (!empty($command) && $command !== ';') {
                    try {
                        $pdo->exec($command);
                        $executedCommands++;
                        
                        // Mostrar progresso a cada 100 comandos
                        if ($executedCommands % 100 === 0) {
                            $this->info("Executados {$executedCommands}/{$totalCommands} comandos...");
                        }
                    } catch (\PDOException $e) {
                        // Ignorar alguns erros comuns que não são críticos
                        if (!$this->isIgnorableError($e->getMessage())) {
                            $this->error("Erro no comando SQL: " . $e->getMessage());
                            $this->error("Comando: " . substr($command, 0, 100) . '...');
                            Log::error('SQL command failed during restore', [
                                'error' => $e->getMessage(),
                                'command' => substr($command, 0, 200)
                            ]);
                            return false;
                        }
                    }
                }
            }

            $this->info("Restauração concluída! Executados {$executedCommands} comandos.");
            return true;
            
        } catch (\Exception $e) {
            $this->error('Erro na restauração: ' . $e->getMessage());
            Log::error('Restore database error', ['exception' => $e]);
            return false;
        }
    }

    /**
     * Criar backup usando PHP nativo (mesmo método do DatabaseBackup)
     */
    private function createBackupWithPHP($filePath, $host, $port, $database, $username, $password)
    {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);

            $backup = "-- KL Gestor Pub Database Backup\n";
            $backup .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
            $backup .= "-- Database: {$database}\n\n";
            $backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
                $backup .= "-- Structure for table `{$table}`\n";
                $backup .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $backup .= $createTable['Create Table'] . ";\n\n";

                $rows = $pdo->query("SELECT * FROM `{$table}`");
                $rowCount = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
                
                if ($rowCount > 0) {
                    $backup .= "-- Data for table `{$table}`\n";
                    $backup .= "LOCK TABLES `{$table}` WRITE;\n";
                    
                    $insertPrefix = "INSERT INTO `{$table}` VALUES ";
                    $values = [];
                    
                    while ($row = $rows->fetch()) {
                        $rowValues = [];
                        foreach ($row as $value) {
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = $pdo->quote($value);
                            }
                        }
                        $values[] = '(' . implode(',', $rowValues) . ')';
                        
                        if (count($values) >= 100) {
                            $backup .= $insertPrefix . implode(',', $values) . ";\n";
                            $values = [];
                        }
                    }
                    
                    if (!empty($values)) {
                        $backup .= $insertPrefix . implode(',', $values) . ";\n";
                    }
                    
                    $backup .= "UNLOCK TABLES;\n\n";
                }
            }

            $backup .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $backup .= "-- End of backup\n";

            return file_put_contents($filePath, $backup) !== false;
            
        } catch (\Exception $e) {
            Log::error('PHP Backup error', ['exception' => $e]);
            return false;
        }
    }

    /**
     * Dividir comandos SQL
     */
    private function splitSqlCommands($sql)
    {
        // Remover comentários
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Dividir por ponto e vírgula, mas preservar strings
        $commands = [];
        $current = '';
        $inString = false;
        $stringChar = null;
        
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            
            if (!$inString && ($char === '"' || $char === "'")) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar && $sql[$i-1] !== '\\') {
                $inString = false;
                $stringChar = null;
            } elseif (!$inString && $char === ';') {
                $commands[] = trim($current);
                $current = '';
                continue;
            }
            
            $current .= $char;
        }
        
        if (trim($current)) {
            $commands[] = trim($current);
        }
        
        return array_filter($commands, function($cmd) {
            return !empty(trim($cmd));
        });
    }

    /**
     * Verificar se o erro pode ser ignorado
     */
    private function isIgnorableError($errorMessage)
    {
        $ignorableErrors = [
            'Table \'.*\' already exists',
            'Unknown table \'.*\'',
            'Duplicate entry',
            'Can\'t DROP',
        ];
        
        foreach ($ignorableErrors as $pattern) {
            if (preg_match('/' . $pattern . '/i', $errorMessage)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Formatar bytes em formato legível
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
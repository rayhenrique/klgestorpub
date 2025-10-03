<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--compress : Compress the backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando backup do banco de dados...');

        try {
            // Configurações do banco de dados
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Nome do arquivo de backup com timestamp
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$database}_{$timestamp}.sql";
            $backupPath = storage_path('app/backups');

            // Criar diretório se não existir
            if (! file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $fullPath = $backupPath.'/'.$filename;

            // Usar abordagem PHP nativa para backup
            $this->info('Criando backup usando PHP nativo...');

            if (! $this->createBackupWithPHP($fullPath, $host, $port, $database, $username, $password)) {
                $this->error('Erro ao criar backup.');

                return 1;
            }

            // Comprimir se solicitado (usando PHP nativo)
            if ($this->option('compress')) {
                $this->info('Comprimindo backup...');
                $originalContent = file_get_contents($fullPath);
                $compressedContent = gzencode($originalContent, 9);

                if ($compressedContent !== false) {
                    $compressedFile = $fullPath.'.gz';
                    if (file_put_contents($compressedFile, $compressedContent) !== false) {
                        unlink($fullPath); // Remove arquivo original
                        $filename .= '.gz';
                        $fullPath = $compressedFile;
                        $this->info('Backup comprimido com sucesso.');
                    }
                }
            }

            // Verificar se o arquivo foi criado
            if (! file_exists($fullPath)) {
                $this->error('Arquivo de backup não foi criado.');

                return 1;
            }

            $fileSize = $this->formatBytes(filesize($fullPath));

            $this->info('Backup criado com sucesso!');
            $this->info("Arquivo: {$filename}");
            $this->info("Tamanho: {$fileSize}");
            $this->info("Local: {$fullPath}");

            // Log da operação
            Log::info('Database backup created', [
                'filename' => $filename,
                'size' => filesize($fullPath),
                'path' => $fullPath,
            ]);

            // Limpar backups antigos (manter últimos 30 dias)
            $this->cleanOldBackups();

            return 0;

        } catch (\Exception $e) {
            $this->error('Erro inesperado: '.$e->getMessage());
            Log::error('Backup error', ['exception' => $e]);

            return 1;
        }
    }

    /**
     * Limpar backups antigos (manter últimos 30 dias)
     */
    private function cleanOldBackups()
    {
        $backupPath = storage_path('app/backups');
        $cutoffDate = Carbon::now()->subDays(30);

        if (! is_dir($backupPath)) {
            return;
        }

        $files = glob($backupPath.'/backup_*.sql*');
        $deletedCount = 0;

        foreach ($files as $file) {
            $fileTime = filemtime($file);
            if ($fileTime < $cutoffDate->timestamp) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Removidos {$deletedCount} backups antigos.");
            Log::info('Old backups cleaned', ['deleted_count' => $deletedCount]);
        }
    }

    /**
     * Criar backup usando PHP nativo
     */
    private function createBackupWithPHP($filePath, $host, $port, $database, $username, $password)
    {
        try {
            // Conectar ao banco de dados
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);

            $backup = "-- KL Gestor Pub Database Backup\n";
            $backup .= '-- Generated on: '.date('Y-m-d H:i:s')."\n";
            $backup .= "-- Database: {$database}\n\n";
            $backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // Obter todas as tabelas
            $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                $this->info("Fazendo backup da tabela: {$table}");

                // Estrutura da tabela
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
                $backup .= "-- Structure for table `{$table}`\n";
                $backup .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $backup .= $createTable['Create Table'].";\n\n";

                // Dados da tabela
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
                        $values[] = '('.implode(',', $rowValues).')';

                        // Inserir em lotes de 100 registros
                        if (count($values) >= 100) {
                            $backup .= $insertPrefix.implode(',', $values).";\n";
                            $values = [];
                        }
                    }

                    // Inserir registros restantes
                    if (! empty($values)) {
                        $backup .= $insertPrefix.implode(',', $values).";\n";
                    }

                    $backup .= "UNLOCK TABLES;\n\n";
                }
            }

            $backup .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $backup .= "-- End of backup\n";

            // Salvar arquivo
            if (file_put_contents($filePath, $backup) === false) {
                return false;
            }

            return true;

        } catch (\Exception $e) {
            $this->error('Erro no backup PHP: '.$e->getMessage());
            Log::error('PHP Backup error', ['exception' => $e]);

            return false;
        }
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

        return round($size, $precision).' '.$units[$i];
    }
}

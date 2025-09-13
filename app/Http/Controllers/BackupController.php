<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class BackupController extends Controller
{
    /**
     * Exibir a página de backup
     */
    public function index()
    {
        $backups = $this->getBackupFiles();
        return view('settings.backup', compact('backups'));
    }

    /**
     * Criar um novo backup
     */
    public function create(Request $request)
    {
        try {
            $compress = $request->boolean('compress', true);
            
            // Executar comando de backup
            $exitCode = Artisan::call('backup:database', [
                '--compress' => $compress
            ]);
            
            if ($exitCode === 0) {
                $output = Artisan::output();
                
                // Extrair nome do arquivo do output
                preg_match('/Arquivo: (.+)/', $output, $matches);
                $filename = $matches[1] ?? 'backup_criado';
                
                Log::info('Backup created via web interface', [
                    'user_id' => auth()->id(),
                    'filename' => $filename,
                    'compressed' => $compress
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Backup criado com sucesso!',
                    'filename' => $filename
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar backup. Verifique os logs.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Backup creation failed', [
                'user_id' => auth()->id(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download de um arquivo de backup
     */
    public function download($filename)
    {
        // Verificar autenticação e permissões
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $backupPath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($backupPath)) {
            abort(404, 'Arquivo de backup não encontrado.');
        }
        
        // Verificar se o arquivo pertence aos backups válidos
        if (!preg_match('/^(backup_|pre_restore_backup_|uploaded_).+\.(sql|sql\.gz)$/', $filename)) {
            abort(403, 'Acesso negado.');
        }
        
        Log::info('Backup downloaded', [
            'user_id' => auth()->id(),
            'filename' => $filename
        ]);
        
        // Determinar o tipo MIME baseado na extensão
        $mimeType = pathinfo($filename, PATHINFO_EXTENSION) === 'gz' 
            ? 'application/gzip' 
            : 'application/sql';
        
        // Ler o arquivo e retornar como resposta direta
        $fileContent = file_get_contents($backupPath);
        $fileSize = filesize($backupPath);
        
        return response($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => $fileSize,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Accept-Ranges' => 'bytes'
        ]);
    }

    /**
     * Restaurar backup
     */
    public function restore(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'confirm' => 'required|accepted'
        ]);
        
        $filename = $request->input('filename');
        
        try {
            // Executar comando de restauração
            $exitCode = Artisan::call('backup:restore', [
                'filename' => $filename,
                '--force' => true
            ]);
            
            if ($exitCode === 0) {
                Log::info('Database restored via web interface', [
                    'user_id' => auth()->id(),
                    'filename' => $filename
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Banco de dados restaurado com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao restaurar backup. Verifique os logs.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Backup restore failed', [
                'user_id' => auth()->id(),
                'filename' => $filename,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload de arquivo de backup externo
     */
    public function upload(Request $request)
    {
        try {
            // Validação customizada para melhor controle de erros
            $validator = Validator::make($request->all(), [
                'backup_file' => [
                    'required',
                    'file',
                    'max:102400', // Max 100MB
                    function ($attribute, $value, $fail) {
                        if (!$value instanceof UploadedFile) {
                            $fail('O arquivo deve ser um arquivo válido.');
                            return;
                        }
                        
                        $extension = strtolower($value->getClientOriginalExtension());
                        $allowedExtensions = ['sql', 'gz'];
                        
                        if (!in_array($extension, $allowedExtensions)) {
                            $fail('O campo backup file deve ser um arquivo do tipo: sql, gz.');
                        }
                    }
                ]
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            
            /** @var UploadedFile $file */
            $file = $request->file('backup_file');
            
            // Verificar se o arquivo foi enviado corretamente
            if (!$file || !$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro no upload do arquivo. Tente novamente.'
                ], 400);
            }
            
            // Gerar nome único para o arquivo
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = "uploaded_{$originalName}_{$timestamp}.{$extension}";
            
            // Criar diretório se não existir
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            // Salvar arquivo usando Storage para melhor controle
            $filePath = $backupPath . '/' . $filename;
            
            // Mover arquivo temporário para destino final
            if (!$file->move($backupPath, $filename)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao salvar o arquivo no servidor.'
                ], 500);
            }
            
            // Verificar se é um arquivo válido
            if (!$this->isValidBackupFile($filePath)) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo inválido. Apenas arquivos de backup SQL são aceitos.'
                ], 400);
            }
            
            // Obter tamanho do arquivo salvo em vez do arquivo temporário
            $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
            
            Log::info('Backup file uploaded', [
                'user_id' => auth()->id(),
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'size' => $fileSize,
                'path' => $filePath
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Arquivo de backup enviado com sucesso!',
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            Log::error('Backup upload failed', [
                'user_id' => auth()->id(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deletar arquivo de backup
     */
    public function delete(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);
        
        $filename = $request->input('filename');
        $backupPath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($backupPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Arquivo não encontrado.'
            ], 404);
        }
        
        try {
            unlink($backupPath);
            
            Log::info('Backup file deleted', [
                'user_id' => auth()->id(),
                'filename' => $filename
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Arquivo de backup deletado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Backup deletion failed', [
                'user_id' => auth()->id(),
                'filename' => $filename,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter lista de arquivos de backup
     */
    private function getBackupFiles()
    {
        $backupPath = storage_path('app/backups');
        
        if (!is_dir($backupPath)) {
            return [];
        }
        
        $files = glob($backupPath . '/*.{sql,sql.gz}', GLOB_BRACE);
        $backups = [];
        
        foreach ($files as $file) {
            $filename = basename($file);
            $backups[] = [
                'filename' => $filename,
                'size' => $this->formatBytes(filesize($file)),
                'size_bytes' => filesize($file),
                'created_at' => Carbon::createFromTimestamp(filemtime($file)),
                'type' => $this->getBackupType($filename)
            ];
        }
        
        // Ordenar por data de criação (mais recente primeiro)
        usort($backups, function($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });
        
        return $backups;
    }

    /**
     * Determinar o tipo de backup
     */
    private function getBackupType($filename)
    {
        if (strpos($filename, 'pre_restore_backup_') === 0) {
            return 'pre-restore';
        } elseif (strpos($filename, 'uploaded_') === 0) {
            return 'uploaded';
        } else {
            return 'automatic';
        }
    }

    /**
     * Verificar se o arquivo é um backup válido
     */
    private function isValidBackupFile($filePath)
    {
        try {
            $content = '';
            
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
            Log::error('Backup validation error', ['exception' => $e, 'file' => $filePath]);
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
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
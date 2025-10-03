@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-database me-2"></i>
                        Backup e Restauração do Banco de Dados
                    </h4>
                    <div>
                        <button type="button" class="btn btn-primary" id="createBackupBtn">
                            <i class="fas fa-plus me-1"></i>
                            Criar Backup
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="fas fa-upload me-1"></i>
                            Enviar Backup
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($backups) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Arquivo</th>
                                        <th>Tipo</th>
                                        <th>Tamanho</th>
                                        <th>Data de Criação</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($backups as $backup)
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-archive me-2 text-muted"></i>
                                                <span class="fw-medium">{{ $backup['filename'] }}</span>
                                            </td>
                                            <td>
                                                @switch($backup['type'])
                                                    @case('automatic')
                                                        <span class="badge bg-success">Automático</span>
                                                        @break
                                                    @case('pre-restore')
                                                        <span class="badge bg-warning">Pré-Restauração</span>
                                                        @break
                                                    @case('uploaded')
                                                        <span class="badge bg-info">Enviado</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Manual</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $backup['size'] }}</td>
                                            <td>
                                                <span title="{{ $backup['created_at']->format('d/m/Y H:i:s') }}">
                                                    {{ $backup['created_at']->diffForHumans() }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('settings.backup.download', $backup['filename']) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success restore-btn" 
                                                            data-filename="{{ $backup['filename'] }}"
                                                            title="Restaurar">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger delete-btn" 
                                                            data-filename="{{ $backup['filename'] }}"
                                                            title="Deletar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-database fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum backup encontrado</h5>
                            <p class="text-muted">Clique em "Criar Backup" para gerar seu primeiro backup do banco de dados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>
                    Enviar Arquivo de Backup
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="backup_file" class="form-label">Arquivo de Backup</label>
                        <input type="file" class="form-control" id="backup_file" name="backup_file" 
                               accept=".sql,.gz" required>
                        <div class="form-text">
                            Aceita arquivos .sql e .sql.gz (máximo 100MB)
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Certifique-se de que o arquivo é um backup válido do banco de dados.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                        <i class="fas fa-upload me-1"></i>
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Restauração -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Restauração
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-circle me-2"></i>ATENÇÃO!</h6>
                    <p class="mb-2">Esta operação irá:</p>
                    <ul class="mb-2">
                        <li>Substituir <strong>TODOS</strong> os dados atuais do banco</li>
                        <li>Criar um backup de segurança antes da restauração</li>
                        <li>Não pode ser desfeita facilmente</li>
                    </ul>
                    <p class="mb-0"><strong>Tem certeza de que deseja continuar?</strong></p>
                </div>
                <p>Arquivo a ser restaurado: <strong id="restoreFilename"></strong></p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmRestore" required>
                    <label class="form-check-label" for="confirmRestore">
                        Eu entendo os riscos e desejo prosseguir com a restauração
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmRestoreBtn" disabled>
                    <i class="fas fa-undo me-1"></i>
                    Restaurar Banco
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza de que deseja deletar o arquivo de backup?</p>
                <p><strong id="deleteFilename"></strong></p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>
                    Deletar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="d-none position-fixed top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5); z-index: 9999;" inert>
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <h5 id="loadingText">Processando...</h5>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
$(document).ready(function() {
    // Criar backup
    $('#createBackupBtn').click(function() {
        showLoading('Criando backup...');
        
        $.ajax({
            url: '{{ route("settings.backup.create") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                compress: true
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                hideLoading();
                const message = xhr.responseJSON?.message || 'Erro ao criar backup';
                showAlert('danger', message);
            }
        });
    });

    // Validação de arquivo no lado cliente
    $('#backup_file').change(function() {
        const file = this.files[0];
        const uploadBtn = $('#uploadBtn');
        const errorDiv = $('#fileError');
        
        // Remover mensagens de erro anteriores
        errorDiv.remove();
        
        if (file) {
            const fileName = file.name.toLowerCase();
            const fileSize = file.size;
            const maxSize = 100 * 1024 * 1024; // 100MB
            
            let isValid = true;
            let errorMessage = '';
            
            // Verificar extensão
            if (!fileName.endsWith('.sql') && !fileName.endsWith('.gz') && !fileName.endsWith('.sql.gz')) {
                isValid = false;
                errorMessage = 'O arquivo deve ter extensão .sql ou .gz';
            }
            
            // Verificar tamanho
            if (fileSize > maxSize) {
                isValid = false;
                errorMessage = 'O arquivo não pode ser maior que 100MB';
            }
            
            if (!isValid) {
                $(this).after(`<div id="fileError" class="text-danger small mt-1">${errorMessage}</div>`);
                uploadBtn.prop('disabled', true);
            } else {
                uploadBtn.prop('disabled', false);
            }
        }
    });

    // Upload de backup
    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        
        const fileInput = $('#backup_file')[0];
        const file = fileInput.files[0];
        
        // Validação final antes do envio
        if (!file) {
            showAlert('danger', 'Por favor, selecione um arquivo.');
            return;
        }
        
        const formData = new FormData(this);
        showLoading('Enviando arquivo...');
        
        // Desabilitar botão durante upload
        $('#uploadBtn').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("settings.backup.upload") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 300000, // 5 minutos timeout
            success: function(response) {
                hideLoading();
                hideBsModal('uploadModal');
                
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', response.message || 'Erro desconhecido no upload');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                
                let message = 'Erro ao enviar arquivo';
                
                if (xhr.status === 422) {
                    // Erro de validação
                    message = xhr.responseJSON?.message || 'Erro de validação do arquivo';
                } else if (xhr.status === 500) {
                    // Erro interno do servidor
                    message = xhr.responseJSON?.message || 'Erro interno do servidor';
                } else if (status === 'timeout') {
                    message = 'Timeout no upload. Arquivo muito grande ou conexão lenta.';
                } else if (xhr.status === 0) {
                    message = 'Erro de conexão. Verifique sua internet.';
                } else {
                    message = xhr.responseJSON?.message || `Erro ${xhr.status}: ${error}`;
                }
                
                showAlert('danger', message);
            },
            complete: function() {
                // Reabilitar botão após conclusão
                $('#uploadBtn').prop('disabled', false);
            }
        });
    });

    // Restaurar backup
    $('.restore-btn').click(function() {
        const filename = $(this).data('filename');
        $('#restoreFilename').text(filename);
        showBsModal('restoreModal');
        
        $('#confirmRestoreBtn').off('click').on('click', function() {
            showLoading('Restaurando banco de dados...');
            hideBsModal('restoreModal');
            
            $.ajax({
                url: '{{ route("settings.backup.restore") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    filename: filename,
                    confirm: 1
                },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        showAlert('success', response.message);
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    const message = xhr.responseJSON?.message || 'Erro ao restaurar backup';
                    showAlert('danger', message);
                }
            });
        });
    });

    // Deletar backup
    $('.delete-btn').click(function() {
        const filename = $(this).data('filename');
        $('#deleteFilename').text(filename);
        showBsModal('deleteModal');
        
        $('#confirmDeleteBtn').off('click').on('click', function() {
            showLoading('Deletando arquivo...');
            hideBsModal('deleteModal');
            
            $.ajax({
                url: '{{ route("settings.backup.delete") }}',
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    filename: filename
                },
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        showAlert('success', response.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    const message = xhr.responseJSON?.message || 'Erro ao deletar arquivo';
                    showAlert('danger', message);
                }
            });
        });
    });

    // Controle do checkbox de confirmação
    $('#confirmRestore').change(function() {
        $('#confirmRestoreBtn').prop('disabled', !this.checked);
    });

    // Funções auxiliares
    function showLoading(text) {
        $('#loadingText').text(text);
        $('#loadingOverlay').removeClass('d-none');
    }

    function hideLoading() {
        $('#loadingOverlay').addClass('d-none');
    }

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.card-body').prepend(alertHtml);
        
        // Auto-hide success alerts
        if (type === 'success') {
            setTimeout(() => {
                $('.alert-success').fadeOut();
            }, 3000);
        }
    }

    // Helpers de compatibilidade para Bootstrap 5 / jQuery plugin
    function getOrCreateModal(id) {
        const el = document.getElementById(id);
        if (!el) return null;
        if (window.bootstrap && typeof bootstrap.Modal !== 'undefined') {
            return bootstrap.Modal.getOrCreateInstance(el);
        }
        return null;
    }

    function showBsModal(id) {
        const el = document.getElementById(id);
        const instance = getOrCreateModal(id);
        if (instance) {
            instance.show();
        } else if (window.$ && $(el).modal) {
            $(el).modal('show');
        }
    }

    function hideBsModal(id) {
        const el = document.getElementById(id);
        const instance = getOrCreateModal(id);
        if (instance) {
            instance.hide();
        } else if (window.$ && $(el).modal) {
            $(el).modal('hide');
        }
    }
});
</script>
@endpush
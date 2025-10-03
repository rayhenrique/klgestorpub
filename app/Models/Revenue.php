<?php

namespace App\Models;

use App\Services\ReportCacheService;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'date',
        'fonte_id',
        'bloco_id',
        'grupo_id',
        'acao_id',
        'observation',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relacionamentos com as categorias
    public function fonte()
    {
        return $this->belongsTo(Category::class, 'fonte_id');
    }

    public function bloco()
    {
        return $this->belongsTo(Category::class, 'bloco_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Category::class, 'grupo_id');
    }

    public function acao()
    {
        return $this->belongsTo(Category::class, 'acao_id');
    }

    // Validação de hierarquia das categorias
    public function validateCategoryHierarchy(): bool
    {
        // Validar se o bloco pertence à fonte especificada
        if ($this->fonte_id && $this->bloco_id) {
            $bloco = Category::find($this->bloco_id);
            if ($bloco && (int) $bloco->parent_id !== (int) $this->fonte_id) {
                return false;
            }
        }

        // Validar se o grupo pertence ao bloco especificado
        if ($this->bloco_id && $this->grupo_id) {
            $grupo = Category::find($this->grupo_id);
            if ($grupo && (int) $grupo->parent_id !== (int) $this->bloco_id) {
                return false;
            }
        }

        // Validar se a ação pertence ao grupo especificado
        if ($this->grupo_id && $this->acao_id) {
            $acao = Category::find($this->acao_id);
            if ($acao && (int) $acao->parent_id !== (int) $this->grupo_id) {
                return false;
            }
        }

        return true;
    }

    // Boot method para adicionar validações automáticas
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($revenue) {
            if (! $revenue->validateCategoryHierarchy()) {
                throw new \Exception('A hierarquia das categorias não está correta. Verifique se as categorias selecionadas seguem a estrutura: Fonte > Bloco > Grupo > Ação.');
            }
        });

        static::booted(function () {
            static::saved(function (Revenue $revenue) {
                app(ReportCacheService::class)->touchRevenue($revenue);
            });

            static::deleted(function (Revenue $revenue) {
                app(ReportCacheService::class)->touchRevenue($revenue);
            });
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'description',
        'amount',
        'date',
        'fonte_id',
        'bloco_id',
        'grupo_id',
        'acao_id',
        'expense_classification_id',
        'observation'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    // Relacionamentos com as categorias
    public function fonte(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'fonte_id');
    }

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'bloco_id');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'grupo_id');
    }

    public function acao(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'acao_id');
    }

    // Relacionamento com a classificação de despesa
    public function classification(): BelongsTo
    {
        return $this->belongsTo(ExpenseClassification::class, 'expense_classification_id');
    }

    // Validação de hierarquia das categorias
    public function validateCategoryHierarchy(): bool
    {
        // Validar se o bloco pertence à fonte especificada
        if ($this->fonte_id && $this->bloco_id) {
            $bloco = Category::find($this->bloco_id);
            if ($bloco && $bloco->parent_id !== $this->fonte_id) {
                return false;
            }
        }

        // Validar se o grupo pertence ao bloco especificado
        if ($this->bloco_id && $this->grupo_id) {
            $grupo = Category::find($this->grupo_id);
            if ($grupo && $grupo->parent_id !== $this->bloco_id) {
                return false;
            }
        }

        // Validar se a ação pertence ao grupo especificado
        if ($this->grupo_id && $this->acao_id) {
            $acao = Category::find($this->acao_id);
            if ($acao && $acao->parent_id !== $this->grupo_id) {
                return false;
            }
        }

        return true;
    }

    // Boot method para adicionar validações automáticas
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($expense) {
            if (!$expense->validateCategoryHierarchy()) {
                throw new \Exception('A hierarquia das categorias não está correta. Verifique se as categorias selecionadas seguem a estrutura: Fonte > Bloco > Grupo > Ação.');
            }
        });
    }
}

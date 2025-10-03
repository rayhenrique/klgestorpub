<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'parent_id',
        'active',
        'description',
    ];

    // Tipos de categoria
    const TYPE_FONTE = 'fonte';

    const TYPE_BLOCO = 'bloco';

    const TYPE_GRUPO = 'grupo';

    const TYPE_ACAO = 'acao';

    // Relação com o pai
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relação com os filhos
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Escopo para cada tipo
    public function scopeFontes($query)
    {
        return $query->where('type', self::TYPE_FONTE);
    }

    public function scopeBlocos($query)
    {
        return $query->where('type', self::TYPE_BLOCO);
    }

    public function scopeGrupos($query)
    {
        return $query->where('type', self::TYPE_GRUPO);
    }

    public function scopeAcoes($query)
    {
        return $query->where('type', self::TYPE_ACAO);
    }

    // Método para obter filhos ativos
    public function getActiveChildren()
    {
        return $this->children()->where('active', true)->get();
    }

    // Método para verificar se pode ter filhos
    public function canHaveChildren(): bool
    {
        return $this->type !== self::TYPE_ACAO;
    }

    // Relações com despesas e receitas
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'acao_id');
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class, 'acao_id');
    }

    // Método para verificar se tem despesas associadas em qualquer nível
    public function hasAssociatedExpenses(): bool
    {
        return Expense::where('fonte_id', $this->id)
            ->orWhere('bloco_id', $this->id)
            ->orWhere('grupo_id', $this->id)
            ->orWhere('acao_id', $this->id)
            ->exists();
    }

    // Método para verificar se tem receitas associadas em qualquer nível
    public function hasAssociatedRevenues(): bool
    {
        return Revenue::where('fonte_id', $this->id)
            ->orWhere('bloco_id', $this->id)
            ->orWhere('grupo_id', $this->id)
            ->orWhere('acao_id', $this->id)
            ->exists();
    }

    // Método para obter o tipo de filho permitido
    public function getAllowedChildType(): ?string
    {
        return match ($this->type) {
            self::TYPE_FONTE => self::TYPE_BLOCO,
            self::TYPE_BLOCO => self::TYPE_GRUPO,
            self::TYPE_GRUPO => self::TYPE_ACAO,
            default => null,
        };
    }

    // Validação antes de excluir categoria
    public function canBeDeleted(): bool
    {
        // Não pode excluir se tem filhos ativos
        if ($this->children()->where('active', true)->exists()) {
            return false;
        }

        // Não pode excluir se tem despesas ou receitas associadas em qualquer nível
        if ($this->hasAssociatedExpenses() || $this->hasAssociatedRevenues()) {
            return false;
        }

        return true;
    }

    // Método para obter mensagem de erro ao tentar excluir
    public function getDeletionErrorMessage(): string
    {
        if ($this->children()->where('active', true)->exists()) {
            return 'Não é possível excluir esta categoria pois ela possui subcategorias ativas.';
        }

        if ($this->hasAssociatedExpenses()) {
            return 'Não é possível excluir esta categoria pois ela possui despesas associadas.';
        }

        if ($this->hasAssociatedRevenues()) {
            return 'Não é possível excluir esta categoria pois ela possui receitas associadas.';
        }

        return '';
    }

    // Boot method para adicionar validações automáticas
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            if (! $category->canBeDeleted()) {
                throw new \Exception($category->getDeletionErrorMessage());
            }
        });
    }
}
